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
	/**
	 * @var array
	 */
	protected static $placeholders = array(
		self::P_USER_FIRSTNAME,
		self::P_USER_LASTNAME,
		self::P_USER_EMAIL,
	);

    /**
     * @throws \arException
     * @deprecated
     */
    public static function returnDbTableName()
    {
        return self::TABLE_NAME;
    }

    public static function getTableName() : string {
        return self::TABLE_NAME;
    }

    /**
	 * @return string
	 */
	public function getConnectorContainerName() {
		return self::TABLE_NAME;
	}


	/**
	 * @param $key
	 *
	 * @return string
	 */
	protected function getPlaceholder($key) {
		switch ($key) {
			case self::P_USER_FIRSTNAME:
				return $this->getUsrObject()->getFirstname();
				break;
			case self::P_USER_LASTNAME:
				return $this->getUsrObject()->getLastname();
				break;
			case self::P_USER_EMAIL:
				return $this->getUsrObject()->getEmail();
				break;
		}

		return '';
	}


	/**
	 * @return string
	 */
	public static function getAvailablePlaceholdersAsString() {
		$return = ilUserDefaultsPlugin::getInstance()->txt('set_placeholders');
		$return .= ' [';
		$return .= implode('] [', self::$placeholders);
		$return .= '] ';

		return $return;
	}


	/**
	 * @return mixed|string
	 */
	public function getReplacesPortfolioTitle() {
		$text = $this->getPortfolioName();

		foreach (self::$placeholders as $p) {
			$text = preg_replace("/\\[" . $p . "\\]/uim", $this->getPlaceholder($p), $text);
		}

		return $text;
	}


	/**
	 * @var ilObjUser
	 */
	protected $usr_object;


	/**
	 * @param int   $primary_key
	 * @param array $add_constructor_args
	 *
	 * @return UserSetting
	 */
	public static function find($primary_key, array $add_constructor_args = array()) {
		return parent::find($primary_key, $add_constructor_args);
	}


	/**
	 *
	 */
	public function delete() {
		foreach ($this->getUdfCheckObjects() as $udf_check) {
			$udf_check->delete();
		}

		parent::delete();
	}


	/**
	 *
	 */
	public function update() {
		$this->setOwner(self::dic()->user()->getId());
		$this->setUpdateDate(time());
		if (!$this->hasChecks() AND $this->getStatus() == self::STATUS_ACTIVE) {
			ilUtil::sendInfo(ilUserDefaultsPlugin::getInstance()->txt('msg_activation_failed'));
			ilUtil::sendInfo(ilUserDefaultsPlugin::getInstance()->txt('msg_activation_failed'), true);
			$this->setStatus(self::STATUS_INACTIVE);
		}
		parent::update();
	}


	/**
	 *
	 */
	public function create() {
		$this->setOwner(self::dic()->user()->getId());
		$this->setUpdateDate(time());
		$this->setCreateDate(time());
		if (!$this->hasChecks()) {
			$this->setStatus(self::STATUS_INACTIVE);
		}
		parent::create();
	}


	/**
	 * @param ilObjUser $user
	 */
	public function doAssignements(ilObjUser $user) {
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


	/**
	 * @param ilObjUser[] $ilObjUsers
	 */
	public function doMultipleAssignements(array $ilObjUsers) {
		foreach ($ilObjUsers as $ilObjUser) {
			if ($ilObjUser instanceof ilObjUser) {
				$this->doAssignements($ilObjUser);
			}
		}
	}


	/**
	 *
	 */
	protected function assignToGlobalRole() {
		$global_roles = $this->getGlobalRoles();
        foreach ($global_roles as $global_role) {
            if (ilObject2::_lookupType($global_role) == 'role') {
                self::dic()->rbac()->admin()->assignUser($global_role, $this->getUsrObject()->getId());
            }
        }
	}

    /**
     *
     */
    protected function unsignGlobalRole() {
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

	/**
	 *
	 */
	protected function assignLocalRoles() {

		$local_roles = $this->getAssignedLocalRoles();
		if (count($local_roles) == 0) {
			return;
		}

		foreach ($local_roles as $local_roles_obj_id) {
			self::dic()->rbac()->admin()->assignUser((int) $local_roles_obj_id, (int) $this->getUsrObject()->getId());
		}
	}

    /**
     *
     */
    protected function unsignLocalRoles() {
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


	/**
	 *
	 */
	protected function assignCourses() {
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


	/**
	 *
	 */
	protected function unsubscribeCourses() {
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

	/**
	 *
	 */
	protected function assignGroups() {
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
				$added = $part->add($usr_id, IL_GRP_MEMBER);
			}
		}

        $this->assignGroupFromQueue();
	}

    protected function unsubscribeGroups() {
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
	    $reference=array_shift(ilObject2::_getAllReferences($id));
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

    /**
     * @return int|null
     */
	protected function assignGroupFromQueue()
    {
        $groups_queue = $this->getAssignedGroupsQueue();
        $part_objs = array_map(function($grp_obj_id) {
            return ilGroupParticipants::_getInstanceByObjId($grp_obj_id);
        }, $groups_queue);
        /** @var ilGroupParticipants $part_obj */
        foreach ($part_objs as $part_obj) {
            if ($part_obj->isMember($this->getUsrObject()->getId())) {
                return null;
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
            $part->add($this->getUsrObject()->getId(), IL_GRP_MEMBER);
            if (!$this->isGroupsQueueDesktop()) {
                $ref_id = array_shift(ilObjGroup::_getAllReferences($group_to_add));

                //ILIAS 5.4
                if(method_exists(ilObjUser::class,'_dropDesktopItem')) {
                    ilObjUser::_dropDesktopItem($this->getUsrObject()->getId(), $ref_id, 'grp');
                } else {
                    self::dic()->favourites()->remove($this->getUsrObject()->getId(), $ref_id);
                }


            }
        }
    }


	/**
	 * @return bool
	 */
	protected function isValid() {
		$do_assignements = true;

		foreach ($this->getUdfCheckObjects() as $udf) {
			if (!$udf->isValid($this->getUsrObject())) {
				$do_assignements = false;
			}
		}

		return $do_assignements;
	}


	/**
	 *
	 */
	protected function generatePortfolio() {
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

    protected function removePortfolio()
    {
        $data = ilObjPortfolio::getPortfoliosOfUser($this->getUsrObject()->getId());
        $ilUser = $this->getUsrObject();
        $target = ilObjPortfolio::getDefaultPortfolio($ilUser->id);
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


	/**
	 * @return bool
	 */
	public function hasChecks() {
		return UDFCheck::hasChecks($this->getId());
	}


	/**
	 *
	 */
	public function afterObjectLoad() {
		$ilUDFChecks = UDFCheck::getChecksByParent($this->getId());
		$this->setUdfCheckObjects($ilUDFChecks);
	}


	/**
	 *
	 */
	protected function addSkills() {
		$user = $this->getUsrObject();
		$skill_ids = $this->getAllPortfolioSkills();

		foreach ($skill_ids as $skill_id) {
			ilPersonalSkill::addPersonalSkill($user->getId(), $skill_id);
		}
	}


	/**
	 * @return array
	 */
	protected function getAllPortfolioSkills() {
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
	 * @return UserSetting
	 * Duplicate this setting and it's dependencies and save everything to the databse.
	 */
	public function duplicate() {
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
	protected $id = 0;
	/**
	 * @var string
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    256
	 */
	protected $title = '';
	/**
	 * @var string
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    1024
	 */
	protected $description = '';
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     1
	 */
	protected $status = self::STATUS_INACTIVE;
	/**
	 * @var array
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     256
	 */
	protected $global_roles = [4];
    /**
     * @var bool
     *
     * @con_has_field true
     * @con_fieldtype integer
     * @con_length    1
     */
    protected $unsign_global_roles = false;
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 */
	protected $owner = 6;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        timestamp
	 * @db_is_notnull       true
	 */
	protected $create_date;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        timestamp
	 * @db_is_notnull       true
	 */
	protected $update_date;
	/**
	 * @var array
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     256
	 */
	protected $assigned_local_roles = array();
    /**
     * @var bool
     *
     * @con_has_field true
     * @con_fieldtype integer
     * @con_length    1
     */
    protected $unsign_local_roles = false;
	/**
	 * @var array
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     256
	 */
	protected $assigned_courses = array();
	/**
	 * @var array
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     256
	 */
	protected $assigned_groupes = array();
	/**
	 * @var bool
	 *
	 * @con_has_field true
	 * @con_fieldtype integer
	 * @con_length    1
	 */
	protected $unsubscr_from_crs_and_cat = false;
    /**
     * @var bool
     *
     * @con_has_field true
     * @con_fieldtype integer
     * @con_length    1
     */
    protected $unsubscr_from_grp = false;
	/**
	 * @var bool
	 *
	 * @con_has_field true
	 * @con_fieldtype integer
	 * @con_length    1
	 */
	protected $assigned_groups_option_request = false;
    /**
     * @var array
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_length     256
     */
	protected $assigned_groups_queue = [];
    /**
     * @var bool
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     1
     */
	protected $groups_queue_desktop = false;
    /**
     * @var bool
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     1
     */
	protected $groups_queue_parallel = false;
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 */
	protected $portfolio_template_id = null;
	/**
	 * @var array
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     256
	 */
	protected $portfolio_assigned_to_groups = array();
	/**
	 * @var string
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    256
	 */
	protected $blog_name = '';
	/**
	 * @var string
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    256
	 */
	protected $portfolio_name = '';
    /**
     * @var bool
     *
     * @con_has_field true
     * @con_fieldtype integer
     * @con_length    1
     */
    protected $remove_from_portfolio = false;
	/**
	 * @var array
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     256
	 */
	protected $assigned_orgus = array();
    /**
     * @var int
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     8
     */
    protected $assigned_orgu_position = null;
    /**
     * @var bool
     *
     * @con_has_field true
     * @con_fieldtype integer
     * @con_length    1
     */
    protected $unsubscribe_from_orgus = false;
	/**
	 * @var array
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     256
	 */
	protected $assigned_studyprograms = array();
    /**
     * @var bool
     *
     * @con_has_field true
     * @con_fieldtype integer
     * @con_length    1
     */
    protected $unsubscr_from_studyprograms = false;
	/**
	 * @var UDFCheck[]
	 */
	protected $udf_check_objects = array();
	/**
	 * @var bool
	 *
	 * @con_has_field true
	 * @con_fieldtype integer
	 * @con_length    1
	 */
	protected $on_create = true;
	/**
	 * @var bool
	 *
	 * @con_has_field true
	 * @con_fieldtype integer
	 * @con_length    1
	 */
	protected $on_update = false;
	/**
	 * @var bool
	 *
	 * @con_has_field true
	 * @con_fieldtype integer
	 * @con_length    1
	 */
	protected $on_manual = true;


	/**
	 * @param $field_name
	 *
	 * @return mixed|null|string
	 */
	public function sleep($field_name) {
		switch ($field_name) {
            case 'global_roles':
            case 'assigned_local_roles':
			case 'assigned_courses':
			case 'assigned_groupes':
			case 'portfolio_assigned_to_groups':
            case 'assigned_groups_queue':
			case 'assigned_orgus':
			case 'assigned_studyprograms':
				return json_encode($this->{$field_name});
				break;
			case 'create_date':
			case 'update_date':
				return date(Config::SQL_DATE_FORMAT, $this->{$field_name});
				break;
		}

		return null;
	}


	/**
	 * @param $field_name
	 * @param $field_value
	 *
	 * @return mixed|null
	 */
	public function wakeUp($field_name, $field_value) {
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


    /**
     * @return bool
     */
    public function isGroupsQueueDesktop() : bool
    {
        return $this->groups_queue_desktop ?? false;
    }


    /**
     * @param bool $groups_queue_desktop
     */
    public function setGroupsQueueDesktop(bool $groups_queue_desktop)
    {
        $this->groups_queue_desktop = $groups_queue_desktop;
    }


    /**
     * @return bool
     */
    public function isGroupsQueueParallel() : bool
    {
        return $this->groups_queue_parallel ?? false;
    }


    /**
     * @param bool $groups_queue_parallel
     */
    public function setGroupsQueueParallel(bool $groups_queue_parallel)
    {
        $this->groups_queue_parallel = $groups_queue_parallel;
    }


	/**
	 * @param string $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}


	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}


	/**
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = $id;
	}


	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * @param int $status
	 */
	public function setStatus($status) {
		$this->status = $status;
	}


	/**
	 * @return int
	 */
	public function getStatus() {
		return $this->status;
	}


	/**
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}


	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}


	/**
	 * @param array $assigned_local_roles
	 */
	public function setAssignedLocalRoles($assigned_local_roles) {
		$this->assigned_local_roles = $assigned_local_roles;
	}


	/**
	 * @return array
	 */
	public function getAssignedLocalRoles() {
		return $this->assigned_local_roles;
	}

    /**
     * @param boolean $unsign_local_roles
     */
    public function setUnsignLocalRoles($unsign_local_roles) {
        $this->unsign_local_roles = $unsign_local_roles;
    }


    /**
     * @return array
     */
    public function isUnsignLocalRoles() {
        return $this->unsign_local_roles;
    }


	/**
	 * @param array $assigned_courses
	 */
	public function setAssignedCourses($assigned_courses) {
		$this->assigned_courses = $assigned_courses;
	}


	/**
	 * @return array
	 */
	public function getAssignedCourses() {
		return $this->assigned_courses;
	}


	/**
	 * @param array $assigned_groupes
	 */
	public function setAssignedGroupes($assigned_groupes) {
		$this->assigned_groupes = $assigned_groupes;
	}


	/**
	 * @return array
	 */
	public function getAssignedGroupes() {
		return $this->assigned_groupes;
	}


	/**
	 * @return bool
	 */
	public function isAssignedGroupsOptionRequest() {
		return $this->assigned_groups_option_request;
	}


	/**
	 * @param bool $assigned_groups_option_request
	 */
	public function setAssignedGroupsOptionRequest($assigned_groups_option_request) {
		$this->assigned_groups_option_request = $assigned_groups_option_request;
	}


    /**
     * @return array
     */
    public function getAssignedGroupsQueue() : array
    {
        return $this->assigned_groups_queue;
    }


    /**
     * @param array $assigned_groups_queue
     */
    public function setAssignedGroupsQueue(array $assigned_groups_queue)
    {
        $this->assigned_groups_queue = $assigned_groups_queue;
    }


	/**
	 * @return bool
	 */
	public function isUnsubscrfromcrsAndcategoriesDesktop() {
		return $this->unsubscr_from_crs_and_cat;
	}

    /**
     * @param bool $unsubscr_from_crs_and_cat
     */
    public function setUnsubscrfromcrsAndcategoriesDesktop($unsubscr_from_crs_and_cat) {
        $this->unsubscr_from_crs_and_cat = $unsubscr_from_crs_and_cat;
    }

    /**
     * @return bool
     */
    public function isUnsubscrfromgrp() {
        return $this->unsubscr_from_grp;
    }

    /**
     * @param bool $unsubscr_from_grp
     */
    public function setUnsubscrfromgrpDesktop($unsubscr_from_grp) {
        $this->unsubscr_from_grp = $unsubscr_from_grp;
    }

	/**
	 * @param UDFCheck[] $udf_check_objects
	 */
	public function setUdfCheckObjects($udf_check_objects) {
		$this->udf_check_objects = $udf_check_objects;
	}


	/**
	 * @return UDFCheck[]
	 */
	public function getUdfCheckObjects() {
		return $this->udf_check_objects;
	}


	/**
	 * @param array $global_roles
	 */
	public function setGlobalRoles($global_roles) {
		$this->global_roles = $global_roles;
	}


	/**
	 * @return array
	 */
	public function getGlobalRoles() {
		return $this->global_roles;
	}

    /**
     * @param array $unsign_global_roles
     */
    public function setUnsignGlobalRoles($unsign_global_roles) {
        $this->unsign_global_roles = $unsign_global_roles;
    }

    /**
     * @return bool
     */
    public function isUnsignGlobalRoles() {
        return $this->unsign_global_roles;
    }

	/**
	 * @param array $portfolio_assigned_to_groups
	 */
	public function setPortfolioAssignedToGroups($portfolio_assigned_to_groups) {
		$this->portfolio_assigned_to_groups = $portfolio_assigned_to_groups;
	}


	/**
	 * @return array
	 */
	public function getPortfolioAssignedToGroups() {
		return $this->portfolio_assigned_to_groups;
	}


	/**
	 * @param int $portfolio_template_id
	 */
	public function setPortfolioTemplateId($portfolio_template_id) {
		$this->portfolio_template_id = $portfolio_template_id;
	}


	/**
	 * @return int
	 */
	public function getPortfolioTemplateId() {
		return $this->portfolio_template_id;
	}


	/**
	 * @param int $create_date
	 */
	public function setCreateDate($create_date) {
		$this->create_date = $create_date;
	}


	/**
	 * @return int
	 */
	public function getCreateDate() {
		return $this->create_date;
	}


	/**
	 * @param int $owner
	 */
	public function setOwner($owner) {
		$this->owner = $owner;
	}


	/**
	 * @return int
	 */
	public function getOwner() {
		return $this->owner;
	}


	/**
	 * @param int $update_date
	 */
	public function setUpdateDate($update_date) {
		$this->update_date = $update_date;
	}


	/**
	 * @return int
	 */
	public function getUpdateDate() {
		return $this->update_date;
	}


	/**
	 * @param ilObjUser $ilObjUser
	 */
	public function setUsrObject($ilObjUser) {
		$this->usr_object = $ilObjUser;
	}


	/**
	 * @return ilObjUser
	 */
	public function getUsrObject() {
		return $this->usr_object;
	}


	/**
	 * @return string
	 */
	public function getBlogName() {
		return $this->blog_name;
	}


	/**
	 * @param string $blog_name
	 */
	public function setBlogName($blog_name) {
		$this->blog_name = $blog_name;
	}


	/**
	 * @return string
	 */
	public function getPortfolioName() {
		return $this->portfolio_name;
	}


	/**
	 * @param string $portfolio_name
	 */
	public function setPortfolioName($portfolio_name) {
		$this->portfolio_name = $portfolio_name;
	}

    /**
     * @return string
     */
    public function getRemovePortfolio() {
        return $this->portfolio_name;
    }


    /**
     * @param string $val
     */
    public function setRemovePortfolio($val) {
        $this->portfolio_name = $val;
    }


	/**
	 * @return array
	 */
	public function getAssignedOrgus() {
		return $this->assigned_orgus;
	}


	/**
	 * @param array $assigned_orgus
	 */
	public function setAssignedOrgus($assigned_orgus) {
		$this->assigned_orgus = $assigned_orgus;
	}

    /**
     * @return int
     */
    public function getAssignedOrguPosition() {
        return $this->assigned_orgu_position;
    }


    /**
     * @param int $id
     */
    public function setAssignedOrguPosition($id) {
        $this->assigned_orgu_position = $id;
    }

    /**
     * @return bool
     */
    public function isUnsubscrFromOrgus() {
        return $this->unsubscribe_from_orgus;
    }

    /**
     * @param bool $state
     */
    public function setUnsubscrFromOrgus($state) {
        $this->unsubscribe_from_orgus = $state;
    }


	/**
	 * @return array
	 */
	public function getAssignedStudyprograms() {
		return $this->assigned_studyprograms;
	}

	/**
	 * @param array $state
	 */
	public function setAssignedStudyprograms($state) {
		$this->assigned_studyprograms = $state;
	}

    /**
     * @return bool
     */
    public function isUnsubscrFromStudyprograms() {
        return $this->unsubscr_from_studyprograms;
    }

    /**
     * @param bool $state
     */
    public function setUnsubscrFromstudyprograms($state) {
        $this->unsubscr_from_studyprograms = $state;
    }

	/**
	 * @return bool
	 */
	public function isOnCreate() {
		return $this->on_create;
	}


	/**
	 * @param bool $on_create
	 */
	public function setOnCreate($on_create) {
		$this->on_create = $on_create;
	}


	/**
	 * @return bool
	 */
	public function isOnUpdate() {
		return $this->on_update;
	}


	/**
	 * @param bool $on_update
	 */
	public function setOnUpdate($on_update) {
		$this->on_update = $on_update;
	}


	/**
	 * @return bool
	 */
	public function isOnManual() {
		return $this->on_manual;
	}


	/**
	 * @param bool $on_manual
	 */
	public function setOnManual($on_manual) {
		$this->on_manual = $on_manual;
	}


	/**
	 * @return bool
	 */
	protected function assignOrgunits() {
		if (!count($this->getAssignedOrgus())) {
			return false;
		}
		foreach ($this->getAssignedOrgus() as $orgu_obj_id) {
			if (ilObject2::_lookupType($orgu_obj_id) != 'orgu') {
				continue;
			}

			$usr_id = $this->getUsrObject()->getId();
			$orgu_ref_ids = ilObjOrgUnit::_getAllReferences($orgu_obj_id);
			$orgu_ref_id = array_shift(array_values($orgu_ref_ids));
			
			if (!$orgu_ref_id) {
				continue;
			}
			$orgUnit = new ilObjOrgUnit($orgu_ref_id, true);
			ilOrgUnitUserAssignment::findOrCreateAssignment($usr_id, (int)$this->getAssignedOrguPosition(), $orgUnit->getRefId());
		}

		return true;
	}

    /**
     * @return bool
     */
    protected function unsubscribeOrgunits() {
        if (!count($this->getAssignedOrgus())) {
            return false;
        }

        foreach ($this->getAssignedOrgus() as $orgu_obj_id) {
            if (ilObject2::_lookupType($orgu_obj_id) != 'orgu') {
                continue;
            }

            $usr_id = $this->getUsrObject()->getId();
            $orgu_ref_ids = ilObjOrgUnit::_getAllReferences($orgu_obj_id);
            $orgu_ref_id = array_shift(array_values($orgu_ref_ids));

            if (!$orgu_ref_id) {
                continue;
            }

            $orgUnit = new ilObjOrgUnit($orgu_ref_id, true);
            $ua = ilOrgUnitUserAssignment::findOrCreateAssignment($usr_id, (int)$this->getAssignedOrguPosition(), $orgUnit->getRefId());
            $ua->delete();
        }

        return true;
    }

	protected function assignStudyprograms() {
		if (!count($this->getAssignedStudyprograms())) {
			return false;
		}
		foreach ($this->getAssignedStudyprograms() as $studyProgramObjId) {
			if (ilObject2::_lookupType($studyProgramObjId) != 'prg') {
				continue;
			}

			$usr_id = $this->getUsrObject()->getId();

			$prg_ref_ids = ilObjStudyProgramme::_getAllReferences($studyProgramObjId);
			$prg_ref_id = array_shift(array_values($prg_ref_ids));
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

    protected function unsubscribeStudyprograms() {

        if (!count($this->getAssignedStudyprograms())) {
            return false;
        }
        foreach ($this->getAssignedStudyprograms() as $studyProgramObjId) {
            if (ilObject2::_lookupType($studyProgramObjId) != 'prg') {
                continue;
            }

            $usr_id = $this->getUsrObject()->getId();

            $prg_ref_ids = ilObjStudyProgramme::_getAllReferences($studyProgramObjId);
            $prg_ref_id = array_shift(array_values($prg_ref_ids));
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



    /**
	 * @return UDFCheck[]
	 */
	protected function copyDependencies($copy) {
		$original_udf_checks = $this->getUdfCheckObjects();
		/** @var UDFCheck[] $new_udf_checks */
		$new_udf_checks = [];
		foreach ($original_udf_checks as $original_udf_check) {
			$new_udf_checks[] = $this->copyUdfCheck($original_udf_check, $copy);
		}

		return $new_udf_checks;
	}


	/**
	 * @param UDFCheck    $original_udf_check
	 * @param UserSetting $parent
	 *
	 * @return mixed
	 */
	protected function copyUdfCheck($original_udf_check, $parent) {
		$next_id = $original_udf_check->getArConnector()->nextID($original_udf_check);
		/** @var UDFCheck $new_udf_check */
		$new_udf_check = $original_udf_check->copy($next_id);
		$new_udf_check->setParentId($parent->getId());
		$new_udf_check->create();

		return $new_udf_check;
	}
}
