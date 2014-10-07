<?php
require_once('./Customizing/global/plugins/Libraries/ActiveRecord/class.ActiveRecord.php');
require_once('./Customizing/global/plugins/Services/EventHandling/EventHook/UserDefaults/classes/class.ilUDFCheck.php');
require_once('./Modules/Portfolio/classes/class.ilObjPortfolio.php');
require_once('./Modules/Portfolio/classes/class.ilObjPortfolioTemplate.php');

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


	/**
	 * @return string
	 */
	static function returnDbTableName() {
		return self::TABLE_NAME;
	}


	/**
	 * @return string
	 */
	public function getConnectorContainerName() {
		return self::TABLE_NAME;
	}


	public function doAssignements() {
		$do_assignements = $this->isValid();
		if ($do_assignements) {
			$this->generatePortfolio();
			$this->assignCourses();
			$this->assignGroups();
		}
	}


	protected function assignCourses() {
		foreach ($this->getAssignedCourses() as $crs_ref_id) {
			$course = new ilObjCourse($crs_ref_id, true);
			$course->getMemberObject()->add($this->getUserObject()->getId(), IL_CRS_MEMBER);
		}
	}


	protected function assignGroups() {
		foreach ($this->getAssignedGroupes() as $grp_ref_id) {
			$group = new ilObjGroup($grp_ref_id, true);
			$group->addMember($this->getUserObject()->getId(), IL_GRP_MEMBER);
		}
	}


	/**
	 * @return bool
	 */
	protected function isValid() {
		$do_assignements = true;
		foreach ($this->getUdfCheckObjects() as $udf) {
			if (! $udf->isValid()) {
				$do_assignements = false;
			}
		}

		return $do_assignements;
	}


	/**
	 * @return ilObjUser
	 */
	protected function getUserObject() {
		//		global $ilUser;

		$ilUser = new ilObjUser(252);

		return $ilUser;
	}


	/**
	 * @throws ilException
	 */
	protected function generatePortfolio() {
		// Generate Portfolio from Template
		$templates = array_keys(ilObjPortfolioTemplate::getAvailablePortfolioTemplates());
		if (! in_array($this->getPortfolioTemplateId(), $templates)) {
			throw new ilException('Portfolio-ID not valid');
		}
		$source = new ilObjPortfolioTemplate($this->getPortfolioTemplateId(), false);
		$target = new ilObjPortfolio();
		$target->setOwner($this->getUserObject()->getId());
		$user = $this->getUserObject();
		$target->setTitle('optes-Angebote für ' . $user->getFirstname() . ' ' . $user->getLastname());
		$target->create();
		$source->clonePagesAndSettings($source, $target);

		//		// Group
		//		$ilPortfolioAccessHandler = new ilPortfolioAccessHandler();
		//		$existing = $ilPortfolioAccessHandler->getPermissions($this->wsp_node_id);
		//
		//		foreach ($this->getPortfolioAssignedToGroups() as $grp_ref_id) {
		//			/**
		//			 * @var $ilGroupParticipants ilGroupParticipants
		//			 */
		//			$ilGroupParticipants = ilGroupParticipants::_getInstanceByObjId(ilObject2::_lookupObjectId($grp_ref_id));
		//			$users = array_merge($ilGroupParticipants->getMembers(), $ilGroupParticipants->getTutors(), $ilGroupParticipants->getAdmins());
		//			foreach ($users as $usr_id) {
		//				if (! in_array($usr_id, $existing)) {
		//					$ilPortfolioAccessHandler->addPermission($this->wsp_node_id, $usr_id);
		//				}
		//			}
		//		}

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
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 */
	protected $portfolio_template_id = 0;
	/**
	 * @var array
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     256
	 */
	protected $portfolio_assigned_to_groups = array();
	/**
	 * @var array
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     1024
	 */
	protected $udf_check_ids = array();
	/**
	 * @var ilUDFCheck[]
	 */
	protected $udf_check_objects = array();


	public function afterObjectLoad() {
		foreach ($this->getUdfCheckIds() as $id) {
			$this->udf_check_objects[] = ilUDFCheck::find($id);
		}
	}


	/**
	 * @param $field_name
	 *
	 * @return mixed|null|string
	 */
	public function sleep($field_name) {
		switch ($field_name) {
			case 'udf_check_ids':
			case 'assigned_courses':
			case 'assigned_groupes':
			case 'portfolio_assigned_to_groups':
				return json_encode($this->{$field_name});
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
			case 'udf_check_ids':
			case 'assigned_courses':
			case 'assigned_groupes':
			case 'portfolio_assigned_to_groups':
				return json_decode($field_value);
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
	 * @param array $udf_check_ids
	 */
	public function setUdfCheckIds($udf_check_ids) {
		$this->udf_check_ids = $udf_check_ids;
	}


	/**
	 * @return array
	 */
	public function getUdfCheckIds() {
		return $this->udf_check_ids;
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
}

?>
