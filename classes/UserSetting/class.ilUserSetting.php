<?php

/**
 * Class ilUserSetting
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class ilUserSetting extends ActiveRecord {

	const TABLE_NAME = 'usr_def_sets';
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
	 * @return string
	 */
	public function getConnectorContainerName() {
		return self::TABLE_NAME;
	}


	/**
	 * @return string
	 * @deprecated
	 */
	public static function returnDbTableName() {
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
	 * @param       $primary_key
	 * @param array $add_constructor_args
	 *
	 * @return ilUserSetting
	 */
	public static function find($primary_key, array $add_constructor_args = array()) {
		return parent::find($primary_key, $add_constructor_args);
	}


	public function delete() {
		foreach ($this->getUdfCheckObjects() as $udf_check) {
			$udf_check->delete();
		}

		parent::delete();
	}


	public function update() {
		global $DIC;
		$ilUser = $DIC->user();
		$this->setOwner($ilUser->getId());
		$this->setUpdateDate(time());
		if (!$this->hasChecks() AND $this->getStatus() == self::STATUS_ACTIVE) {
			ilUtil::sendInfo(ilUserDefaultsPlugin::getInstance()->txt('msg_activation_failed'));
			ilUtil::sendInfo(ilUserDefaultsPlugin::getInstance()->txt('msg_activation_failed'), true);
			$this->setStatus(self::STATUS_INACTIVE);
		}
		parent::update();
	}


	public function create() {
		global $DIC;
		$ilUser = $DIC->user();
		$this->setOwner($ilUser->getId());
		$this->setUpdateDate(time());
		$this->setCreateDate(time());
		if (!$this->hasChecks()) {
			$this->setStatus(self::STATUS_INACTIVE);
		}
		parent::create();
	}


	/**
	 * @param ilObjUser $ilObjUser
	 */
	public function doAssignements(ilObjUser $ilObjUser) {
		$this->setUsrObject($ilObjUser);
		if ($this->isValid()) {
			$this->addSkills();
			$this->generatePortfolio();
			$this->assignCourses();
			$this->assignGroups();
			$this->assignToGlobalRole();
			$this->assignOrgunits();
			$this->assignStudyprograms();
		} else {
			if ($this->isUnsubscribeCoursesDesktop()) {
				$this->unsubscribeCourses();
			}
		}
	}


	/**
	 * @param \ilObjUser[] $ilObjUsers
	 */
	public function doMultipleAssignements(array $ilObjUsers) {
		foreach ($ilObjUsers as $ilObjUser) {
			if ($ilObjUser instanceof ilObjUser) {
				$this->doAssignements($ilObjUser);
			}
		}
	}


	protected function assignToGlobalRole() {
		global $DIC;
		$rbacadmin = $DIC->rbac()->admin();

		$global_role = $this->getGlobalRole();
		if (ilObject2::_lookupType($global_role) == 'role') {
			$rbacadmin->assignUser($global_role, $this->getUsrObject()->getId());
		}
	}


	protected function assignCourses() {
		$courses = array_merge($this->getAssignedCourses(), $this->getAssignedCoursesDesktop());
		if (count($courses) == 0) {
			return false;
		}

		foreach ($courses as $crs_obj_id) {
			if ($crs_obj_id == "" || ilObject2::_lookupType($crs_obj_id) != 'crs') {
				continue;
			}
			$part = ilCourseParticipants::_getInstanceByObjId($crs_obj_id);
			$usr_id = $this->getUsrObject()->getId();
			$added = $part->add($usr_id, ilCourseConstants::CRS_MEMBER);

			if (!in_array($crs_obj_id, $this->getAssignedCoursesDesktop()) && $added) {
				$all_refs = ilObject2::_getAllReferences($crs_obj_id);
				$first = array_shift(array_values($all_refs));
				ilObjUser::_dropDesktopItem($usr_id, $first, 'crs');
			}
		}
	}


	protected function unsubscribeCourses() {
		if (!$this->isUnsubscribeCoursesDesktop()) {
			return false;
		}

		$courses = array_merge($this->getAssignedCourses(), $this->getAssignedCoursesDesktop());
		if (count($courses) == 0) {
			return false;
		}

		foreach ($courses as $crs_obj_id) {
			if ($crs_obj_id == "" || ilObject2::_lookupType($crs_obj_id) != 'crs') {
				continue;
			}
			$part = ilCourseParticipants::_getInstanceByObjId($crs_obj_id);
			$usr_id = $this->getUsrObject()->getId();
			$added = $part->deleteParticipants(array( $usr_id ));
		}
	}


	protected function assignGroups() {
		$groups = array_merge($this->getAssignedGroupes(), $this->getAssignedGroupesDesktop());
		if (count($groups) == 0) {
			return false;
		}

		foreach ($groups as $grp_obj_id) {
			if ($grp_obj_id == "" || ilObject2::_lookupType($grp_obj_id) != 'grp') {
				continue;
			}
			$part = ilGroupParticipants::_getInstanceByObjId($grp_obj_id);
			$usr_id = $this->getUsrObject()->getId();
			$added = $part->add($usr_id, IL_GRP_MEMBER);

			if (!in_array($grp_obj_id, $this->getAssignedGroupesDesktop()) && $added) {
				$all_refs = ilObject2::_getAllReferences($grp_obj_id);
				$first = array_shift(array_values($all_refs));
				ilObjUser::_dropDesktopItem($usr_id, $first, 'grp');
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
	 * @throws ilException
	 */
	protected function generatePortfolio() {
		global $DIC;
		$ilUser = $DIC->user();
		$ilSetting = $DIC["ilSetting"];

		if ($this->getPortfolioTemplateId() < 10) {
			return false;
		}

		$data = ilObjPortfolio::getPortfoliosOfUser($this->getUsrObject()->getId());

		foreach ($data as $p) {
			if (trim($p['title']) == trim($this->getReplacesPortfolioTitle())) {
				return false;
			}
		}

		$backup_user = $ilUser;
		$ilUser = $this->getUsrObject();

		$prtt_id = $this->getPortfolioTemplateId();
		$recipe = NULL;
		foreach (ilPortfolioTemplatePage::getAllPortfolioPages($prtt_id) as $page) {
			switch ($page["type"]) {
				case ilPortfolioTemplatePage::TYPE_BLOG_TEMPLATE:
					if (!$ilSetting->get('disable_wsp_blogs')) {
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

		// $recipe["skills"] = (array)$form->getInput("skill_ids");

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


	/**
	 * @return bool
	 */
	public function hasChecks() {
		return ilUDFCheck::where(array( 'parent_id' => $this->getId() ))->hasSets();
	}


	public function afterObjectLoad() {
		$ilUDFChecks = ilUDFCheck::where(array( 'parent_id' => $this->getId() ))->get();
		$this->setUdfCheckObjects($ilUDFChecks);
	}


	/**
	 * @return array
	 */
	protected function addSkills() {
		$user = $this->getUsrObject();
		$pskills = array_keys(ilPersonalSkill::getSelectedUserSkills($user->getId()));
		$skill_ids = array();
		$recipe = array();
		foreach (ilPortfolioTemplatePage::getAllPages('prtt', $this->getPortfolioTemplateId()) as $page) {
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

		foreach ($skill_ids as $skill_id) {
			ilPersonalSkill::addPersonalSkill($user->getId(), $skill_id);
		}
	}


	/**
	 * @return ilUserSetting
	 * Duplicate this setting and it's dependencies and save everything to the databse.
	 */
	public function duplicate() {
		/**
		 * @var $copy ilUserSetting
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
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 */
	protected $global_role = 4;
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
	protected $assigned_courses = array();
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     256
	 */
	protected $assigned_groupes = array();
	/**
	 * @var array
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     256
	 */
	protected $assigned_courses_desktop = array();
	/**
	 * @var bool
	 *
	 * @con_has_field true
	 * @con_fieldtype integer
	 * @con_length    1
	 */
	protected $unsubscribe_courses_desktop = false;
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     256
	 */
	protected $assigned_groupes_desktop = array();
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 */
	protected $portfolio_template_id = NULL;
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
	 * @var array
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     256
	 */
	protected $assigned_orgus = array();
	/**
	 * @var array
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     256
	 */
	protected $assigned_studyprograms = array();
	/**
	 * @var ilUDFCheck[]
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
			case 'assigned_courses':
			case 'assigned_courses_desktop':
			case 'assigned_groupes':
			case 'assigned_groupes_desktop':
			case 'portfolio_assigned_to_groups':
			case 'assigned_orgus':
			case 'assigned_studyprograms':
				return json_encode($this->{$field_name});
				break;
			case 'create_date':
			case 'update_date':
				return date("Y-m-d H:i:s", $this->{$field_name});
				break;
		}

		return NULL;
	}


	/**
	 * @param $field_name
	 * @param $field_value
	 *
	 * @return mixed|null
	 */
	public function wakeUp($field_name, $field_value) {
		switch ($field_name) {
			case 'assigned_courses':
			case 'assigned_groupes':
			case 'portfolio_assigned_to_groups':
			case 'assigned_orgus':
			case 'assigned_studyprograms':
			case 'assigned_courses_desktop':
			case 'assigned_groupes_desktop':
				$json_decode = json_decode($field_value);

				return is_array($json_decode) ? $json_decode : array();
				break;
			case 'create_date':
			case 'update_date':
				return strtotime($field_value);
				break;
		}

		return NULL;
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
	 * @return array
	 */
	public function getAssignedCoursesDesktop() {
		return $this->assigned_courses_desktop;
	}


	/**
	 * @param array $assigned_courses_desktop
	 */
	public function setAssignedCoursesDesktop($assigned_courses_desktop) {
		$this->assigned_courses_desktop = $assigned_courses_desktop;
	}


	/**
	 * @return bool
	 */
	public function isUnsubscribeCoursesDesktop() {
		return $this->unsubscribe_courses_desktop;
	}


	/**
	 * @param bool $unsubscribe_courses_desktop
	 */
	public function setUnsubscribeCoursesDesktop($unsubscribe_courses_desktop) {
		$this->unsubscribe_courses_desktop = $unsubscribe_courses_desktop;
	}


	/**
	 * @return int
	 */
	public function getAssignedGroupesDesktop() {
		return $this->assigned_groupes_desktop;
	}


	/**
	 * @param int $assigned_groupes_desktop
	 */
	public function setAssignedGroupesDesktop($assigned_groupes_desktop) {
		$this->assigned_groupes_desktop = $assigned_groupes_desktop;
	}


	/**
	 * @param \ilUDFCheck[] $udf_check_objects
	 */
	public function setUdfCheckObjects($udf_check_objects) {
		$this->udf_check_objects = $udf_check_objects;
	}


	/**
	 * @return \ilUDFCheck[]
	 */
	public function getUdfCheckObjects() {
		return $this->udf_check_objects;
	}


	/**
	 * @param int $global_role
	 */
	public function setGlobalRole($global_role) {
		$this->global_role = $global_role;
	}


	/**
	 * @return int
	 */
	public function getGlobalRole() {
		return $this->global_role;
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
	 * @param \ilObjUser $ilObjUser
	 */
	public function setUsrObject($ilObjUser) {
		$this->usr_object = $ilObjUser;
	}


	/**
	 * @return \ilObjUser
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
	 * @return array
	 */
	public function getAssignedStudyprograms() {
		return $this->assigned_studyprograms;
	}


	/**
	 * @param array $assigned_studyprogramms
	 */
	public function setAssignedStudyprograms($assigned_studyprogramms) {
		$this->assigned_studyprograms = $assigned_studyprogramms;
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
			$orgUnit->assignUsersToEmployeeRole(array( $usr_id ));
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
			$studyProgram->assignUser($usr_id, 6);
		}

		return true;
	}


	/**
	 * @return ilUDFCheck[]
	 */
	protected function copyDependencies($copy) {
		$original_udf_checks = $this->getUdfCheckObjects();
		/** @var ilUDFCheck[] $new_udf_checks */
		$new_udf_checks = [];
		foreach ($original_udf_checks as $original_udf_check) {
			$new_udf_checks[] = $this->copyUdfCheck($original_udf_check, $copy);
		}

		return $new_udf_checks;
	}


	/**
	 * @param $original_udf_check ilUdfCheck
	 * @param $parent             ilUserSetting
	 *
	 * @return mixed
	 */
	protected function copyUdfCheck($original_udf_check, $parent) {
		$next_id = $original_udf_check->getArConnector()->nextID($original_udf_check);
		/** @var ilUDFCheck $new_udf_check */
		$new_udf_check = $original_udf_check->copy($next_id);
		$new_udf_check->setParentId($parent->getId());
		$new_udf_check->create();

		return $new_udf_check;
	}
}
