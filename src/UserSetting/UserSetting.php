<?php

namespace srag\Plugins\UserDefaults\UserSetting;

use ActiveRecord;
use DOMXPath;
use ilCourseConstants;
use ilCourseParticipants;
use ilExAssignment;
use ilExSubmission;
use ilGroupParticipants;
use ilObjCourse;
use ilObject2;

//use ilObject;
use ilObjExercise;
use ilObjGroup;
use ilObjOrgUnit;
use ilObjPortfolio;
use ilObjPortfolioTemplate;
use ilObjStudyProgramme;
use ilObjUser;
use ilOrgUnitUserAssignment;
use ilParticipants;
use ilPersonalSkill;
use ilPortfolioAccessHandler;
use ilPortfolioTemplatePage;
use ilUserDefaultsPlugin;
use ilUtil;
use php4DOMDocument;
use srag\DIC\UserDefaults\DICTrait;
use srag\Plugins\UserDefaults\Access\Courses;
use srag\Plugins\UserDefaults\UDFCheck\UDFCheck;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;
use srag\ActiveRecordConfig\UserDefaults\Config\Config;
use ilRbacReview;

/**
 * Class ilUserSetting
 *
 * @package srag\Plugins\UserDefaults\UserSetting
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class UserSetting extends ActiveRecord {

	use DICTrait;
	use UserDefaultsTrait;
	const TABLE_NAME = 'usr_def_sets';
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
	const STATUS_INACTIVE = 1;
	const STATUS_ACTIVE = 2;
	const P_USER_FIRSTNAME = 'FIRSTNAME';
	const P_USER_LASTNAME = 'LASTNAME';
	const P_USER_EMAIL = 'EMAIL';
	protected static array $placeholders = array(
		self::P_USER_FIRSTNAME,
		self::P_USER_LASTNAME,
		self::P_USER_EMAIL,
	);

    /**
     * @deprecated
     */
    public static function returnDbTableName(): string
    {
        return self::TABLE_NAME;
    }

    public static function getTableName() : string {
        return self::TABLE_NAME;
    }

	public function getConnectorContainerName(): string
    {
		return self::TABLE_NAME;
	}


	protected function getPlaceholder(string $key): string
    {
        return match ($key) {
            self::P_USER_FIRSTNAME => $this->getUsrObject()->getFirstname(),
            self::P_USER_LASTNAME => $this->getUsrObject()->getLastname(),
            self::P_USER_EMAIL => $this->getUsrObject()->getEmail(),
            default => '',
        };
    }

	public static function getAvailablePlaceholdersAsString(): string
    {
		$return = ilUserDefaultsPlugin::getInstance()->txt('set_placeholders');
		$return .= ' [';
		$return .= implode('] [', self::$placeholders);
		$return .= '] ';

		return $return;
	}

	public function getReplacesPortfolioTitle(): string {
		$text = $this->getPortfolioName();

		foreach (self::$placeholders as $p) {
			$text = preg_replace("/\\[" . $p . "\\]/uim", $this->getPlaceholder($p), $text);
		}

		return $text;
	}

	protected ilObjUser $usr_object;


	/**
	 * @param int   $primary_key
	 * @param array $add_constructor_args
	 *
	 * @return UserSetting
	 */
	public static function find($primary_key, array $add_constructor_args = array()): ?ActiveRecord
    {
		return parent::find($primary_key, $add_constructor_args);
	}

	public function delete(): void
    {
		foreach ($this->getUdfCheckObjects() as $udf_check) {
			$udf_check->delete();
		}

		parent::delete();
	}

	public function update(): void
    {
		$this->setOwner(self::dic()->user()->getId());
		$this->setUpdateDate(time());
		if (!$this->hasChecks() AND $this->getStatus() == self::STATUS_ACTIVE) {
            global $DIC;
            $tpl = $DIC["tpl"];
            $tpl->setOnScreenMessage('info', ilUserDefaultsPlugin::getInstance()->txt('msg_activation_failed'), true);
			$this->setStatus(self::STATUS_INACTIVE);
		}
		parent::update();
	}

	public function create(): void
    {
		$this->setOwner(self::dic()->user()->getId());
		$this->setUpdateDate(time());
		$this->setCreateDate(time());
		if (!$this->hasChecks()) {
			$this->setStatus(self::STATUS_INACTIVE);
		}
		parent::create();
	}

	public function doAssignements(ilObjUser $user): void
    {
		$this->setUsrObject($user);
		if ($this->isValid()) {
			$this->generatePortfolio();
			$this->assignLocalRoles();
			$this->assignCourses();
			$this->assignGroups();
			$this->assignToGlobalRole();
			$this->assignOrgunits();
			$this->assignStudyprograms();
		} else {
			if ($this->isUnsubscrfromcrsAndcategoriesDesktop()) {
				$this->unsubscribeCourses();
			}

			if ($this->isUnsubscrfromgrp()) {
                $this->unsubscribeGroups();
            }

			if ($this->isUnsignLocalRoles()) {
			    $this->unsignLocalRoles();
            }

			if ($this->isUnsignGlobalRoles()) {
                $this->unsignGlobalRole();
            }

			if ($this->isUnsubscrFromOrgus()) {
                $this->unsubscribeOrgunits();
            }

            if ($this->isUnsubscrFromStudyprograms()) {
                $this->unsubscribeStudyprograms();
            }

            if ($this->getRemovePortfolio()) {
                $this->removePortfolio();
            }
		}
	}

	public function doMultipleAssignements(array $ilObjUsers): void
    {
		foreach ($ilObjUsers as $ilObjUser) {
			if ($ilObjUser instanceof ilObjUser) {
				$this->doAssignements($ilObjUser);
			}
		}
	}

	protected function assignToGlobalRole(): void
    {
		$global_roles = $this->getGlobalRoles();
        foreach ($global_roles as $global_role) {
            if (ilObject2::_lookupType($global_role) == 'role') {
                self::dic()->rbac()->admin()->assignUser($global_role, $this->getUsrObject()->getId());
            }
        }
	}

    protected function unsignGlobalRole(): void
    {
        if (!$this->isUnsignGlobalRoles()) {
            return;
        }

        $global_roles = $this->getGlobalRoles();
        foreach ($global_roles as $global_role) {
            if (ilObject2::_lookupType($global_role) == 'role') {
                self::dic()->rbac()->admin()->deassignUser($global_role, $this->getUsrObject()->getId());
            }
        }
    }

	protected function assignLocalRoles(): void
    {

		$local_roles = $this->getAssignedLocalRoles();
		if (count($local_roles) == 0) {
			return;
		}

		foreach ($local_roles as $local_roles_obj_id) {
			self::dic()->rbac()->admin()->assignUser((int) $local_roles_obj_id, (int) $this->getUsrObject()->getId());
		}
	}

    protected function unsignLocalRoles(): void
    {
        if (!$this->isUnsignLocalRoles()) {
            return;
        }

        $local_roles = $this->getAssignedLocalRoles();

        if (count($local_roles) == 0) {
            return;
        }

        foreach ($local_roles as $local_roles_obj_id) {
            self::dic()->rbac()->admin()->deassignUser((int) $local_roles_obj_id, (int) $this->getUsrObject()->getId());
        }
    }

	protected function assignCourses(): void
    {
		$courses = $this->getAssignedCourses();
		if (count($courses) == 0) {
			return;
		}

		foreach ($courses as $crs_obj_id) {
			if ($crs_obj_id == "" || ilObject2::_lookupType($crs_obj_id) != Courses::TYPE_CRS) {
				continue;
			}
			$crs = new ilObjCourse($crs_obj_id,false);

			$part =new ilCourseParticipants($crs_obj_id);

			$usr_id = $this->getUsrObject()->getId();
			$added = $part->add($usr_id, ilCourseConstants::CRS_MEMBER);

			$crs->checkLPStatusSync($usr_id);
		}
	}

	protected function unsubscribeCourses(): void
    {
		if (!$this->isUnsubscrfromcrsAndcategoriesDesktop()) {
			return;
		}

		$courses = $this->getAssignedCourses();
		if (count($courses) == 0) {
			return;
		}

		foreach ($courses as $crs_obj_id) {
			if ($crs_obj_id === "" || ilObject2::_lookupType($crs_obj_id) !== Courses::TYPE_CRS) {
				continue;
			}
			$part = ilCourseParticipants::_getInstanceByObjId($crs_obj_id);
			$usr_id = $this->getUsrObject()->getId();
            if (!$part->isMember($usr_id)) {
                continue;
            }
			$added = $part->deleteParticipants(array( $usr_id ));
		}
	}

	protected function assignGroups(): void
    {
        $groups = $this->getAssignedGroupes();

        foreach ($groups as $grp_obj_id) {
			if ($grp_obj_id == "" || ilObject2::_lookupType($grp_obj_id) != 'grp') {
				continue;
			}
			$part = ilGroupParticipants::_getInstanceByObjId($grp_obj_id);
			$usr_id = $this->getUsrObject()->getId();

			if ($this->isAssignedGroupsOptionRequest()) {
				//ilGroupMembershipMailNotification::TYPE_NOTIFICATION_REGISTRATION_REQUEST,
				$added = $part->addSubscriber($usr_id);
				$part->updateSubscriptionTime($usr_id, time());
				$part->sendNotification(31, $usr_id);
			} else {
				$added = $part->add($usr_id, ilParticipants::IL_GRP_MEMBER);
			}
		}

        $this->assignGroupFromQueue();
	}

    protected function unsubscribeGroups(): void
    {
        if (!$this->isUnsubscrfromgrp()) {
            return;
        }
        $groups = $this->getAssignedGroupes();
        if (count($groups) === 0) {
            return;
        }

        foreach ($groups as $id) {
            if ($id === "" || ilObject2::_lookupType($id) !== "grp") {
                continue;
            }
	    $usr_id = $this->getUsrObject()->getId();
            $references = ilObject2::_getAllReferences($id);
            $reference= array_shift($references);
	    $groupRoles = self::dic()->rbac()->review()->getRolesOfRoleFolder($reference);
	    foreach ($groupRoles as $grouprole) {
		    if (ilObject2::_lookupTitle($grouprole) == 'il_grp_member_'.$reference) {
			    $memberRole = $grouprole;
	    		    self::dic()->rbac()->admin()->deassignUser($memberRole,$usr_id);
			    continue;
		    }
	    }
        }
    }


	protected function assignGroupFromQueue(): void
    {
        $groups_queue = $this->getAssignedGroupsQueue();
        $part_objs = array_map(function($grp_obj_id) {
            return ilGroupParticipants::_getInstanceByObjId($grp_obj_id);
        }, $groups_queue);
        /** @var ilGroupParticipants $part_obj */
        foreach ($part_objs as $part_obj) {
            if ($part_obj->isMember($this->getUsrObject()->getId())) {
                return;
            }
        }

        $group_to_add = null;
        if ($this->isGroupsQueueParallel()) {
            // take group with lowest member count & not full (or last if every group is full)
            $min_member_count = null;
            foreach ($groups_queue as $grp_obj_id) {
                $ilObjGroup = new ilObjGroup($grp_obj_id, false);
                $part = ilGroupParticipants::_getInstanceByObjId($grp_obj_id);
                if ($part->getCountMembers() >= $ilObjGroup->getMaxMembers()) {
                    continue;
                }
                if (!is_int($min_member_count) || $part->getCountMembers() < $min_member_count) {
                    $group_to_add = $grp_obj_id;
                    $min_member_count = $part->getCountMembers();
                }
            }
            $group_to_add = $group_to_add ?? end($groups_queue);
        } else {
            // take first group which is not full (or last group if every group is full)
            foreach ($groups_queue as $grp_obj_id) {
                $part = ilGroupParticipants::_getInstanceByObjId($grp_obj_id);
                $ilObjGroup = new ilObjGroup($grp_obj_id, false);
                $group_to_add = $ilObjGroup->getId();
                if (!$ilObjGroup->getMaxMembers() || $part->getCountMembers() < $ilObjGroup->getMaxMembers()) {
                    break;
                }
            }
        }

        if (is_int($group_to_add)) {
            $part = ilGroupParticipants::_getInstanceByObjId($group_to_add);
            $part->add($this->getUsrObject()->getId(), ilParticipants::IL_GRP_MEMBER);
            if (!$this->isGroupsQueueDesktop()) {
                $allReferences = ilObjGroup::_getAllReferences($group_to_add);
                $ref_id = array_shift($allReferences);

                //ILIAS 5.4
                if(method_exists(ilObjUser::class,'_dropDesktopItem')) {
                    ilObjUser::_dropDesktopItem($this->getUsrObject()->getId(), $ref_id, 'grp');
                } else {
                    self::dic()->favourites()->remove($this->getUsrObject()->getId(), $ref_id);
                }


            }
        }
    }

	protected function isValid(): bool
    {
		$do_assignements = true;

		foreach ($this->getUdfCheckObjects() as $udf) {
			if (!$udf->isValid($this->getUsrObject())) {
				$do_assignements = false;
			}
		}

		return $do_assignements;
	}

	protected function generatePortfolio(): void
    {
		if ($this->getPortfolioTemplateId() < 10) {
			return;
		}

		$data = ilObjPortfolio::getPortfoliosOfUser($this->getUsrObject()->getId());

		foreach ($data as $p) {
			if (trim($p['title']) == trim($this->getReplacesPortfolioTitle())) {
				return;
			}
		}

		$backup_user = self::dic()->user();
		$ilUser = $this->getUsrObject();

		$prtt_id = $this->getPortfolioTemplateId();
		$recipe = null;
		foreach (ilPortfolioTemplatePage::getAllPortfolioPages($prtt_id) as $page) {
			switch ($page["type"]) {
				case ilPortfolioTemplatePage::TYPE_BLOG_TEMPLATE:
					if (!self::dic()->settings()->get('disable_wsp_blogs')) {
						$field_id = "blog_" . $page["id"];

						$recipe[$page["id"]] = array(
							"blog",
							"create",
							$page['title'],
						);
					}
					break;
			}
		}

		$recipe["skills"] = $this->getAllPortfolioSkills();

		$source = new ilObjPortfolioTemplate($prtt_id, false);

		// create portfolio
		$target = new ilObjPortfolio();
		$target->setTitle($this->getReplacesPortfolioTitle());
		$target->setOnline(true);
		$target->setDefault(true);
		$target->setOwner($ilUser->getId());
		$target->create();
		$target_id = $target->getId();

		$source->clonePagesAndSettings($source, $target, $recipe);

		// link portfolio to exercise assignment
		$exc_ref_id = (int)$_REQUEST["exc_id"];
		$ass_id = (int)$_REQUEST["ass_id"];

		$exc = new ilObjExercise($exc_ref_id);
		$ass = new ilExAssignment($ass_id);
		if ($ass->getExerciseId() == $exc->getId()
			&& $ass->getType() == ilExAssignment::TYPE_PORTFOLIO) {
			// #16205
			$sub = new ilExSubmission($ass, $ilUser->getId());
			$sub->addResourceObject($target_id);
		}

		ilObjPortfolio::setUserDefault($ilUser->getId(), $target->getId());

		// Set permissions
		$ilPortfolioAccessHandler = new ilPortfolioAccessHandler();
		foreach ($this->getPortfolioAssignedToGroups() as $grp_obj_id) {
			if (ilObject2::_lookupType($grp_obj_id) == 'grp') {
				$ilPortfolioAccessHandler->removePermission($target->getId(), $grp_obj_id);
				$ilPortfolioAccessHandler->addPermission($target->getId(), $grp_obj_id);
			}
		}

		$ilUser = $backup_user;
	}

    protected function removePortfolio(): void
    {
        $data = ilObjPortfolio::getPortfoliosOfUser($this->getUsrObject()->getId());
        $ilUser = $this->getUsrObject();
        $target = ilObjPortfolio::getDefaultPortfolio($ilUser->getId());
        $access_handler = new ilPortfolioAccessHandler();

        foreach ($data as $p) {
            if (trim($p['title']) == trim($this->getReplacesPortfolioTitle())) {
                if ($p['id'] != $target) {
                    return;
                }
                $access_handler->removePermission($target);

                $portfolio = new ilObjPortfolio($target, false);
                $portfolio->delete();
                return;
            }
        }
    }

	public function hasChecks(): bool
    {
		return UDFCheck::hasChecks($this->getId());
	}

	public function afterObjectLoad(): void
    {
		$ilUDFChecks = UDFCheck::getChecksByParent($this->getId());
		$this->setUdfCheckObjects($ilUDFChecks);
	}

	protected function addSkills(): void
    {
		$user = $this->getUsrObject();
		$skill_ids = $this->getAllPortfolioSkills();

		foreach ($skill_ids as $skill_id) {
			ilPersonalSkill::addPersonalSkill($user->getId(), $skill_id);
		}
	}


	protected function getAllPortfolioSkills(): array
    {
		$user = $this->getUsrObject();
		$pskills = array_keys(ilPersonalSkill::getSelectedUserSkills($user->getId()));
		$skill_ids = array();
		foreach (ilPortfolioTemplatePage::getAllPortfolioPages($this->getPortfolioTemplateId()) as $page) {
			switch ($page['type']) {
				case ilPortfolioTemplatePage::TYPE_PAGE:
					$source_page = new ilPortfolioTemplatePage($page['id']);
					$source_page->buildDom(true);
					$dom = $source_page->getDom();
					if ($dom instanceof php4DOMDocument) {
						$dom = $dom->myDOMDocument;
					}
					$xpath = new DOMXPath($dom);
					$nodes = $xpath->query('//PageContent/Skills');
					foreach ($nodes as $node) {
						$skill_id = $node->getAttribute('Id');
						if (!in_array($skill_id, $pskills)) {
							$skill_ids[] = $skill_id;
						}
					}
					unset($nodes);
					unset($xpath);
					unset($dom);
					break;
			}
		}

		return $skill_ids;
	}


	/**
	 * Duplicate this setting and it's dependencies and save everything to the databse.
	 */
	public function duplicate(): UserSetting
    {
		/**
		 * @var UserSetting $copy
		 */
		$next_id = $this->getArConnector()->nextID($this);
		$copy = $this->copy($next_id);
		$copy->setTitle($this->getTitle() . ' (2)');
		$copy->create();
		$this->copyDependencies($copy);

		return $copy;
	}


	/**
	 * @var int
	 *
	 * @con_is_primary true
	 * @con_is_unique  true
	 * @con_has_field  true
	 * @con_sequence   true
	 * @con_fieldtype  integer
	 * @con_length     8
	 */
	protected ?int $id = 0;
	/**
	 * @var string
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    256
	 */
	protected string $title = '';
	/**
	 * @var string
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    1024
	 */
	protected string $description = '';
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     1
	 */
	protected int $status = self::STATUS_INACTIVE;
	/**
	 * @var array
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     256
	 */
	protected array $global_roles = [4];
    /**
     * @var bool
     *
     * @con_has_field true
     * @con_fieldtype integer
     * @con_length    1
     */
    protected bool $unsign_global_roles = false;
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 */
	protected int $owner = 6;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        timestamp
	 * @db_is_notnull       true
	 */
	protected int $create_date = 0;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        timestamp
	 * @db_is_notnull       true
	 */
	protected int $update_date = 0;
	/**
	 * @var array
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     256
	 */
	protected array $assigned_local_roles = array();
    /**
     * @var bool
     *
     * @con_has_field true
     * @con_fieldtype integer
     * @con_length    1
     */
    protected bool $unsign_local_roles = false;
	/**
	 * @var array
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     256
	 */
	protected array $assigned_courses = array();
	/**
	 * @var array
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     256
	 */
	protected array $assigned_groupes = array();
	/**
	 * @var bool
	 *
	 * @con_has_field true
	 * @con_fieldtype integer
	 * @con_length    1
	 */
	protected bool $unsubscr_from_crs_and_cat = false;
    /**
     * @var bool
     *
     * @con_has_field true
     * @con_fieldtype integer
     * @con_length    1
     */
    protected bool $unsubscr_from_grp = false;
	/**
	 * @var bool
	 *
	 * @con_has_field true
	 * @con_fieldtype integer
	 * @con_length    1
	 */
	protected bool $assigned_groups_option_request = false;
    /**
     * @var array
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_length     256
     */
	protected array $assigned_groups_queue = [];
    /**
     * @var bool
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     1
     */
	protected bool $groups_queue_desktop = false;
    /**
     * @var bool
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     1
     */
	protected bool $groups_queue_parallel = false;
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 */
	protected ?int $portfolio_template_id = null;
	/**
	 * @var array
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     256
	 */
	protected array $portfolio_assigned_to_groups = array();
	/**
	 * @var string
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    256
	 */
	protected string $blog_name = '';
	/**
	 * @var string
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    256
	 */
	protected string $portfolio_name = '';
    /**
     * @var bool
     *
     * @con_has_field true
     * @con_fieldtype integer
     * @con_length    1
     */
    protected bool $remove_from_portfolio = false;
	/**
	 * @var array
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     256
	 */
	protected array $assigned_orgus = array();
    /**
     * @var int
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     8
     */
    protected ?int $assigned_orgu_position = null;
    /**
     * @var bool
     *
     * @con_has_field true
     * @con_fieldtype integer
     * @con_length    1
     */
    protected bool $unsubscribe_from_orgus = false;
	/**
	 * @var array
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     256
	 */
	protected array $assigned_studyprograms = array();
    /**
     * @var bool
     *
     * @con_has_field true
     * @con_fieldtype integer
     * @con_length    1
     */
    protected bool $unsubscr_from_studyprograms = false;
	/**
	 * @var UDFCheck[]
	 */
	protected array $udf_check_objects = array();
	/**
	 * @var bool
	 *
	 * @con_has_field true
	 * @con_fieldtype integer
	 * @con_length    1
	 */
	protected bool $on_create = true;
	/**
	 * @var bool
	 *
	 * @con_has_field true
	 * @con_fieldtype integer
	 * @con_length    1
	 */
	protected bool $on_update = false;
	/**
	 * @var bool
	 *
	 * @con_has_field true
	 * @con_fieldtype integer
	 * @con_length    1
	 */
	protected bool $on_manual = true;


	public function sleep($field_name): string|bool|null
    {
        return match ($field_name) {
            'global_roles', 'assigned_local_roles', 'assigned_courses', 'assigned_groupes', 'portfolio_assigned_to_groups', 'assigned_groups_queue', 'assigned_orgus', 'assigned_studyprograms' => json_encode($this->{$field_name}),
            'create_date', 'update_date' => date(Config::SQL_DATE_FORMAT, $this->{$field_name}),
            default => null,
        };

    }


	/**
	 * @param $field_name
	 * @param $field_value
     */
	public function wakeUp($field_name, $field_value): array|bool|int|null
    {
		switch ($field_name) {
			case 'global_roles':
			case 'assigned_local_roles':
			case 'assigned_courses':
			case 'assigned_groupes':
            case 'assigned_groups_queue':
			case 'portfolio_assigned_to_groups':
			case 'assigned_orgus':
			case 'assigned_studyprograms':
				$json_decode = json_decode($field_value, true);

				return is_array($json_decode) ? $json_decode : array();
				break;
			case 'create_date':
			case 'update_date':
				return strtotime($field_value);
				break;
		}

		return null;
	}

    public function isGroupsQueueDesktop() : bool
    {
        return $this->groups_queue_desktop ?? false;
    }


    public function setGroupsQueueDesktop(bool $groups_queue_desktop): void
    {
        $this->groups_queue_desktop = $groups_queue_desktop;
    }

    public function isGroupsQueueParallel() : bool
    {
        return $this->groups_queue_parallel ?? false;
    }


    public function setGroupsQueueParallel(bool $groups_queue_parallel): void
    {
        $this->groups_queue_parallel = $groups_queue_parallel;
    }

	public function setDescription(string $description): void
    {
		$this->description = $description;
	}

	public function getDescription(): string
    {
		return $this->description;
	}

	public function setId(int $id): void
    {
		$this->id = $id;
	}

	public function getId(): int
    {
		return $this->id;
	}


	public function setStatus(int $status): void
    {
		$this->status = $status;
	}

	public function getStatus(): int
    {
		return $this->status;
	}


	public function setTitle(string $title): void
    {
		$this->title = $title;
	}

	public function getTitle(): string
    {
		return $this->title;
	}


	public function setAssignedLocalRoles(array $assigned_local_roles): void
    {
		$this->assigned_local_roles = $assigned_local_roles;
	}

	public function getAssignedLocalRoles(): array
    {
		return $this->assigned_local_roles;
	}

    public function setUnsignLocalRoles(bool $unsign_local_roles): void
    {
        $this->unsign_local_roles = $unsign_local_roles;
    }

    public function isUnsignLocalRoles(): bool|array
    {
        return $this->unsign_local_roles;
    }

	public function setAssignedCourses($assigned_courses): void
    {
		$this->assigned_courses = $assigned_courses;
	}

	public function getAssignedCourses(): array
    {
		return $this->assigned_courses;
	}


	public function setAssignedGroupes(array $assigned_groupes): void
    {
		$this->assigned_groupes = $assigned_groupes;
	}

	public function getAssignedGroupes(): array
    {
		return $this->assigned_groupes;
	}

	public function isAssignedGroupsOptionRequest(): bool
    {
		return $this->assigned_groups_option_request;
	}

	public function setAssignedGroupsOptionRequest(bool $assigned_groups_option_request): void
    {
		$this->assigned_groups_option_request = $assigned_groups_option_request;
	}

    public function getAssignedGroupsQueue() : array
    {
        return $this->assigned_groups_queue;
    }

    public function setAssignedGroupsQueue(array $assigned_groups_queue): void
    {
        $this->assigned_groups_queue = $assigned_groups_queue;
    }

	public function isUnsubscrfromcrsAndcategoriesDesktop(): bool
    {
		return $this->unsubscr_from_crs_and_cat;
	}

    public function setUnsubscrfromcrsAndcategoriesDesktop(bool $unsubscr_from_crs_and_cat): void
    {
        $this->unsubscr_from_crs_and_cat = $unsubscr_from_crs_and_cat;
    }

    public function isUnsubscrfromgrp(): bool
    {
        return $this->unsubscr_from_grp;
    }

    public function setUnsubscrfromgrpDesktop(bool $unsubscr_from_grp): void
    {
        $this->unsubscr_from_grp = $unsubscr_from_grp;
    }

	/**
	 * @param UDFCheck[] $udf_check_objects
	 */
	public function setUdfCheckObjects(array $udf_check_objects): void
    {
		$this->udf_check_objects = $udf_check_objects;
	}

	public function getUdfCheckObjects(): array
    {
		return $this->udf_check_objects;
	}

	public function setGlobalRoles(array $global_roles): void
    {
		$this->global_roles = $global_roles;
	}

	public function getGlobalRoles(): array
    {
		return $this->global_roles;
	}

    public function setUnsignGlobalRoles(bool $unsign_global_roles): void
    {
        $this->unsign_global_roles = $unsign_global_roles;
    }

    public function isUnsignGlobalRoles(): bool
    {
        return $this->unsign_global_roles;
    }

	public function setPortfolioAssignedToGroups(array $portfolio_assigned_to_groups): void
    {
		$this->portfolio_assigned_to_groups = $portfolio_assigned_to_groups;
	}

	public function getPortfolioAssignedToGroups(): array
    {
		return $this->portfolio_assigned_to_groups;
	}

	public function setPortfolioTemplateId(int $portfolio_template_id): void
    {
		$this->portfolio_template_id = $portfolio_template_id;
	}


	public function getPortfolioTemplateId(): ?int
    {
		return $this->portfolio_template_id;
	}

	public function setCreateDate(int $create_date): void
    {
		$this->create_date = $create_date;
	}

	public function getCreateDate(): int
    {
		return $this->create_date;
	}

	public function setOwner(int $owner): void
    {
		$this->owner = $owner;
	}

	public function getOwner(): int
    {
		return $this->owner;
	}

	public function setUpdateDate(int $update_date): void
    {
		$this->update_date = $update_date;
	}

	public function getUpdateDate(): int
    {
		return $this->update_date;
	}

	public function setUsrObject(ilObjUser $ilObjUser): void
    {
		$this->usr_object = $ilObjUser;
	}

	public function getUsrObject(): ilObjUser
    {
		return $this->usr_object;
	}

	public function getBlogName(): string
    {
		return $this->blog_name;
	}

	public function setBlogName(string $blog_name): void
    {
		$this->blog_name = $blog_name;
	}

	public function getPortfolioName(): string
    {
		return $this->portfolio_name;
	}

	public function setPortfolioName(string $portfolio_name): void
    {
		$this->portfolio_name = $portfolio_name;
	}

    public function getRemovePortfolio(): string
    {
        return $this->portfolio_name;
    }

    public function setRemovePortfolio(string $val): void
    {
        $this->portfolio_name = $val;
    }

	public function getAssignedOrgus(): array
    {
		return $this->assigned_orgus;
	}

	public function setAssignedOrgus(array $assigned_orgus): void
    {
		$this->assigned_orgus = $assigned_orgus;
	}

    public function getAssignedOrguPosition(): ?int
    {
        return $this->assigned_orgu_position;
    }

    public function setAssignedOrguPosition(int $id): void
    {
        $this->assigned_orgu_position = $id;
    }

    public function isUnsubscrFromOrgus(): bool
    {
        return $this->unsubscribe_from_orgus;
    }

    public function setUnsubscrFromOrgus(bool $state): void
    {
        $this->unsubscribe_from_orgus = $state;
    }

	public function getAssignedStudyprograms(): array
    {
		return $this->assigned_studyprograms;
	}

	public function setAssignedStudyprograms(array $state): void
    {
		$this->assigned_studyprograms = $state;
	}

    public function isUnsubscrFromStudyprograms(): bool
    {
        return $this->unsubscr_from_studyprograms;
    }

    public function setUnsubscrFromstudyprograms(bool $state): void
    {
        $this->unsubscr_from_studyprograms = $state;
    }

	public function isOnCreate(): bool
    {
		return $this->on_create;
	}


	public function setOnCreate(bool $on_create): void
    {
		$this->on_create = $on_create;
	}

	public function isOnUpdate(): bool
    {
		return $this->on_update;
	}

	public function setOnUpdate(bool $on_update): void
    {
		$this->on_update = $on_update;
	}

	public function isOnManual(): bool
    {
		return $this->on_manual;
	}

	public function setOnManual(bool $on_manual): void
    {
		$this->on_manual = $on_manual;
	}

	protected function assignOrgunits(): bool
    {
		if (!count($this->getAssignedOrgus())) {
			return false;
		}
		foreach ($this->getAssignedOrgus() as $orgu_obj_id) {
			if (ilObject2::_lookupType((int) $orgu_obj_id) != 'orgu') {
				continue;
			}

			$usr_id = $this->getUsrObject()->getId();
			$orgu_ref_ids = ilObjOrgUnit::_getAllReferences($orgu_obj_id);
            $array_values = array_values($orgu_ref_ids);
            $orgu_ref_id = array_shift($array_values);
			
			if (!$orgu_ref_id) {
				continue;
			}
			$orgUnit = new ilObjOrgUnit($orgu_ref_id, true);
			ilOrgUnitUserAssignment::findOrCreateAssignment($usr_id, (int)$this->getAssignedOrguPosition(), $orgUnit->getRefId());
		}

		return true;
	}

    protected function unsubscribeOrgunits(): bool
    {
        if (!count($this->getAssignedOrgus())) {
            return false;
        }

        foreach ($this->getAssignedOrgus() as $orgu_obj_id) {
            if (ilObject2::_lookupType($orgu_obj_id) != 'orgu') {
                continue;
            }

            $usr_id = $this->getUsrObject()->getId();
            $orgu_ref_ids = ilObjOrgUnit::_getAllReferences($orgu_obj_id);
            $array_values = array_values($orgu_ref_ids);
            $orgu_ref_id = array_shift($array_values);

            if (!$orgu_ref_id) {
                continue;
            }

            $orgUnit = new ilObjOrgUnit($orgu_ref_id, true);
            $ua = ilOrgUnitUserAssignment::findOrCreateAssignment($usr_id, (int)$this->getAssignedOrguPosition(), $orgUnit->getRefId());
            $ua->delete();
        }

        return true;
    }

	protected function assignStudyprograms(): bool
    {
		if (!count($this->getAssignedStudyprograms())) {
			return false;
		}
		foreach ($this->getAssignedStudyprograms() as $studyProgramObjId) {
			if (ilObject2::_lookupType((int) $studyProgramObjId) != 'prg') {
				continue;
			}

			$usr_id = $this->getUsrObject()->getId();

			$prg_ref_ids = ilObjStudyProgramme::_getAllReferences($studyProgramObjId);
            $array_values = array_values($prg_ref_ids);
            $prg_ref_id = array_shift($array_values);
			if (!$prg_ref_id) {
				continue;
			}
			$studyProgram = new ilObjStudyProgramme($prg_ref_id, true);

			if ($studyProgram->isActive() && !$studyProgram->hasAssignmentOf($usr_id)) {
                $studyProgram->assignUser($usr_id, 6);
            }
		}

		return true;
	}

    protected function unsubscribeStudyprograms(): bool
    {

        if (!count($this->getAssignedStudyprograms())) {
            return false;
        }
        foreach ($this->getAssignedStudyprograms() as $studyProgramObjId) {
            if (ilObject2::_lookupType($studyProgramObjId) != 'prg') {
                continue;
            }

            $usr_id = $this->getUsrObject()->getId();

            $prg_ref_ids = ilObjStudyProgramme::_getAllReferences($studyProgramObjId);
            $array_values = array_values($prg_ref_ids);
            $prg_ref_id = array_shift($array_values);
            if (!$prg_ref_id) {
                continue;
            }
            $studyProgram = new ilObjStudyProgramme($prg_ref_id, true);

            if ($studyProgram->isActive()) {
                $assignments = $studyProgram->getAssignmentsOf($usr_id);

                if ($assignments != NULL) {
                    foreach ($assignments as $assignment)
                    $studyProgram->removeAssignment($assignment);
                }
            }
        }

        return true;
    }

	protected function copyDependencies($copy): array
    {
		$original_udf_checks = $this->getUdfCheckObjects();
		/** @var UDFCheck[] $new_udf_checks */
		$new_udf_checks = [];
		foreach ($original_udf_checks as $original_udf_check) {
			$new_udf_checks[] = $this->copyUdfCheck($original_udf_check, $copy);
		}

		return $new_udf_checks;
	}

	protected function copyUdfCheck(UDFCheck $original_udf_check, UserSetting $parent): UDFCheck {
		$next_id = $original_udf_check->getArConnector()->nextID($original_udf_check);
		/** @var UDFCheck $new_udf_check */
		$new_udf_check = $original_udf_check->copy($next_id);
		$new_udf_check->setParentId($parent->getId());
		$new_udf_check->create();

		return $new_udf_check;
	}
}
