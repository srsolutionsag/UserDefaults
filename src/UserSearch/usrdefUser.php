<?php

namespace srag\Plugins\UserDefaults\UserSearch;

use ActiveRecord;
use ilException;
use ilUserDefaultsPlugin;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

/**
 * Class usrdefUser
 *
 * @deprecated TODO: Remove this class
 */
class usrdefUser extends ActiveRecord {

	use UserDefaultsTrait;
	/**
	 * @var string
	 *
	 * @deprecated
	 */
	const TABLE_NAME = "usr_data";
	/**
	 * @var string
	 *
	 * @deprecated
	 */
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;


	/**
	 * @return string
	 *
	 * @deprecated
	 */
	public function getConnectorContainerName(): string
    {
		return self::TABLE_NAME;
	}


	/**
	 * @return string
	 *
	 * @deprecated
	 */
	public static function returnDbTableName(): string
    {
		return self::TABLE_NAME;
	}


	/**
	 * @var usrdefObj
	 *
	 * @deprecated
	 */
	protected usrdefObj $usrdefObj;


	/**
	 * @deprecated
	 */
	public function create(): void
    {
		throw new ilException('ActiveReacord Class ' . __CLASS__ . ' is not allowed to ' . __METHOD__ . ' objects');
	}


	/**
	 * @deprecated
	 */
	public function update(): void {
		throw new ilException('ActiveReacord Class ' . __CLASS__ . ' is not allowed to ' . __METHOD__ . ' objects');
	}


	/**
	 * @deprecated
	 */
	public function delete(): void {
		throw new ilException('ActiveReacord Class ' . __CLASS__ . ' is not allowed to ' . __METHOD__ . ' objects');
	}


	/**
	 * @deprecated
	 */
	public function afterObjectLoad(): void
    {
		$this->setusrdefObj(usrdefObj::find($this->getUsrId()));
	}


	/**
	 * @var
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       4
	 * @con_sequence     true
	 * @con_is_notnull   true
	 * @con_is_primary   true
	 * @con_is_unique    true
	 *
	 * @deprecated
	 */
	protected ?int $usr_id;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    80
	 *
	 * @deprecated
	 */
	protected string $login;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    32
	 *
	 * @deprecated
	 */
	protected string $passwd;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    32
	 *
	 * @deprecated
	 */
	protected string $firstname;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    32
	 *
	 * @deprecated
	 */
	protected string $lastname;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    32
	 *
	 * @deprecated
	 */
	protected string $title;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    1
	 *
	 * @deprecated
	 */
	protected string $gender;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    80
	 *
	 * @deprecated
	 */
	protected string $email;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    80
	 *
	 * @deprecated
	 */
	protected string $institution;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    40
	 *
	 * @deprecated
	 */
	protected string $street;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    40
	 *
	 * @deprecated
	 */
	protected string $city;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    10
	 *
	 * @deprecated
	 */
	protected string $zipcode;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    40
	 *
	 * @deprecated
	 */
	protected string $country;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    40
	 *
	 * @deprecated
	 */
	protected string $phone_office;

	/**
	 * @var
	 *
	 * @con_has_field true
     * @con_fieldtype text
	 *
	 * @deprecated
	 */
	protected string $last_update;

	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    4000
	 *
	 * @deprecated
	 */
	protected string $hobby;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    80
	 *
	 * @deprecated
	 */
	protected string $department;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    40
	 *
	 * @deprecated
	 */
	protected string $phone_home;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    40
	 *
	 * @deprecated
	 */
	protected string $phone_mobile;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    40
	 *
	 * @deprecated
	 */
	protected string $fax;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    32
	 *
	 * @deprecated
	 */
	protected string $i2passwd;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype integer
	 * @con_length    4
	 *
	 * @deprecated
	 */
	protected int $time_limit_owner;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype integer
	 * @con_length    4
	 *
	 * @deprecated
	 */
	protected int $time_limit_unlimited;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype integer
	 * @con_length    4
	 *
	 * @deprecated
	 */
	protected ?int $time_limit_from;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype integer
	 * @con_length    4
	 *
	 * @deprecated
	 */
	protected ?int $time_limit_until;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype integer
	 * @con_length    4
	 *
	 * @deprecated
	 */
	protected int $time_limit_message;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    250
	 *
	 * @deprecated
	 */
	protected string $referral_comment;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    40
	 *
	 * @deprecated
	 */
	protected string $matriculation;
	/**
	 * @var
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     4
	 * @con_is_notnull true
	 *
	 * @deprecated
	 */
	protected int $active;

	/**
	 * @var
	 *
	 * @con_has_field true
     * @con_fieldtype text
	 *
	 * @deprecated
	 */
	protected ?string $agree_date;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype integer
	 * @con_length    4
	 *
	 * @deprecated
	 */
	protected int $ilinc_id;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    40
	 *
	 * @deprecated
	 */
	protected string $ilinc_login;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    40
	 *
	 * @deprecated
	 */
	protected string $ilinc_passwd;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    255
	 *
	 * @deprecated
	 */
	protected string $client_ip;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    10
	 *
	 * @deprecated
	 */
	protected string $auth_mode;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype integer
	 * @con_length    4
	 *
	 * @deprecated
	 */
	protected int $profile_incomplete;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    250
	 *
	 * @deprecated
	 */
	protected string $ext_account;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    40
	 *
	 * @deprecated
	 */
	protected string $im_icq;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    40
	 *
	 * @deprecated
	 */
	protected string $im_yahoo;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    40
	 *
	 * @deprecated
	 */
	protected string $im_msn;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    40
	 *
	 * @deprecated
	 */
	protected string $im_aim;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    40
	 *
	 * @deprecated
	 */
	protected string $im_skype;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    32
	 *
	 * @deprecated
	 */
	protected ?string $feed_hash;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    40
	 *
	 * @deprecated
	 */
	protected string $delicious;


	/**
	 * @var
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     4
	 * @con_is_notnull true
	 *
	 * @deprecated
	 */
	protected int $loc_zoom;
	/**
	 * @var
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     1
	 * @con_is_notnull true
	 *
	 * @deprecated
	 */
	protected int $login_attempts;
	/**
	 * @var
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     4
	 * @con_is_notnull true
	 *
	 * @deprecated
	 */
	protected int $last_password_change;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    40
	 *
	 * @deprecated
	 */
	protected string $im_jabber;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    40
	 *
	 * @deprecated
	 */
	protected string $im_voip;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    32
	 *
	 * @deprecated
	 */
	protected ?string $reg_hash;
	/**
	 * @var
	 *
	 * @con_has_field true
     * @con_fieldtype text
	 *
	 * @deprecated
	 */
	protected ?string $birthday;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    2
	 *
	 * @deprecated
	 */
	protected string $sel_country;

	/**
	 * @var
	 *
	 * @con_has_field true
     * @con_fieldtype text
	 *
	 * @deprecated
	 */
	protected ?string $inactivation_date;
	/**
	 * @var
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     1
	 * @con_is_notnull true
	 *
	 * @deprecated
	 */
	protected int $is_self_registered;


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getActive() {
		return $this->active;
	}


	/**
	 * @param mixed $active
	 *
	 * @deprecated
	 */
	public function setActive($active) {
		$this->active = $active;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getAgreeDate() {
		return $this->agree_date;
	}


	/**
	 * @param mixed $agree_date
	 *
	 * @deprecated
	 */
	public function setAgreeDate($agree_date) {
		$this->agree_date = $agree_date;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getApproveDate() {
		return $this->approve_date;
	}


	/**
	 * @param mixed $approve_date
	 *
	 * @deprecated
	 */
	public function setApproveDate($approve_date) {
		$this->approve_date = $approve_date;
	}


	/**
	 * @return usrdefObj
	 *
	 * @deprecated
	 */
	public function getusrdefObj() {
		return $this->usrdefObj;
	}


	/**
	 * @param usrdefObj $usrdefObj
	 *
	 * @deprecated
	 */
	public function setusrdefObj($usrdefObj) {
		$this->usrdefObj = $usrdefObj;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getAuthMode() {
		return $this->auth_mode;
	}


	/**
	 * @param mixed $auth_mode
	 *
	 * @deprecated
	 */
	public function setAuthMode($auth_mode) {
		$this->auth_mode = $auth_mode;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getBirthday() {
		return $this->birthday;
	}


	/**
	 * @param mixed $birthday
	 *
	 * @deprecated
	 */
	public function setBirthday($birthday) {
		$this->birthday = $birthday;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getCity() {
		return $this->city;
	}


	/**
	 * @param mixed $city
	 *
	 * @deprecated
	 */
	public function setCity($city) {
		$this->city = $city;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getClientIp() {
		return $this->client_ip;
	}


	/**
	 * @param mixed $client_ip
	 *
	 * @deprecated
	 */
	public function setClientIp($client_ip) {
		$this->client_ip = $client_ip;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getCountry() {
		return $this->country;
	}


	/**
	 * @param mixed $country
	 *
	 * @deprecated
	 */
	public function setCountry($country) {
		$this->country = $country;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getCreateDate() {
		return $this->create_date;
	}


	/**
	 * @param mixed $create_date
	 *
	 * @deprecated
	 */
	public function setCreateDate($create_date) {
		$this->create_date = $create_date;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getDelicious() {
		return $this->delicious;
	}


	/**
	 * @param mixed $delicious
	 *
	 * @deprecated
	 */
	public function setDelicious($delicious) {
		$this->delicious = $delicious;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getDepartment() {
		return $this->department;
	}


	/**
	 * @param mixed $department
	 *
	 * @deprecated
	 */
	public function setDepartment($department) {
		$this->department = $department;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getEmail() {
		return $this->email;
	}


	/**
	 * @param mixed $email
	 *
	 * @deprecated
	 */
	public function setEmail($email) {
		$this->email = $email;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getExtAccount() {
		return $this->ext_account;
	}


	/**
	 * @param mixed $ext_account
	 *
	 * @deprecated
	 */
	public function setExtAccount($ext_account) {
		$this->ext_account = $ext_account;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getFax() {
		return $this->fax;
	}


	/**
	 * @param mixed $fax
	 *
	 * @deprecated
	 */
	public function setFax($fax) {
		$this->fax = $fax;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getFeedHash() {
		return $this->feed_hash;
	}


	/**
	 * @param mixed $feed_hash
	 *
	 * @deprecated
	 */
	public function setFeedHash($feed_hash) {
		$this->feed_hash = $feed_hash;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getFirstname() {
		return $this->firstname;
	}


	/**
	 * @param mixed $firstname
	 *
	 * @deprecated
	 */
	public function setFirstname($firstname) {
		$this->firstname = $firstname;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getGender() {
		return $this->gender;
	}


	/**
	 * @param mixed $gender
	 *
	 * @deprecated
	 */
	public function setGender($gender) {
		$this->gender = $gender;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getHobby() {
		return $this->hobby;
	}


	/**
	 * @param mixed $hobby
	 *
	 * @deprecated
	 */
	public function setHobby($hobby) {
		$this->hobby = $hobby;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getI2passwd() {
		return $this->i2passwd;
	}


	/**
	 * @param mixed $i2passwd
	 *
	 * @deprecated
	 */
	public function setI2passwd($i2passwd) {
		$this->i2passwd = $i2passwd;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getIlincId() {
		return $this->ilinc_id;
	}


	/**
	 * @param mixed $ilinc_id
	 *
	 * @deprecated
	 */
	public function setIlincId($ilinc_id) {
		$this->ilinc_id = $ilinc_id;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getIlincLogin() {
		return $this->ilinc_login;
	}


	/**
	 * @param mixed $ilinc_login
	 *
	 * @deprecated
	 */
	public function setIlincLogin($ilinc_login) {
		$this->ilinc_login = $ilinc_login;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getIlincPasswd() {
		return $this->ilinc_passwd;
	}


	/**
	 * @param mixed $ilinc_passwd
	 *
	 * @deprecated
	 */
	public function setIlincPasswd($ilinc_passwd) {
		$this->ilinc_passwd = $ilinc_passwd;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getImAim() {
		return $this->im_aim;
	}


	/**
	 * @param mixed $im_aim
	 *
	 * @deprecated
	 */
	public function setImAim($im_aim) {
		$this->im_aim = $im_aim;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getImIcq() {
		return $this->im_icq;
	}


	/**
	 * @param mixed $im_icq
	 *
	 * @deprecated
	 */
	public function setImIcq($im_icq) {
		$this->im_icq = $im_icq;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getImJabber() {
		return $this->im_jabber;
	}


	/**
	 * @param mixed $im_jabber
	 *
	 * @deprecated
	 */
	public function setImJabber($im_jabber) {
		$this->im_jabber = $im_jabber;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getImMsn() {
		return $this->im_msn;
	}


	/**
	 * @param mixed $im_msn
	 *
	 * @deprecated
	 */
	public function setImMsn($im_msn) {
		$this->im_msn = $im_msn;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getImSkype() {
		return $this->im_skype;
	}


	/**
	 * @param mixed $im_skype
	 *
	 * @deprecated
	 */
	public function setImSkype($im_skype) {
		$this->im_skype = $im_skype;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getImVoip() {
		return $this->im_voip;
	}


	/**
	 * @param mixed $im_voip
	 *
	 * @deprecated
	 */
	public function setImVoip($im_voip) {
		$this->im_voip = $im_voip;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getImYahoo() {
		return $this->im_yahoo;
	}


	/**
	 * @param mixed $im_yahoo
	 *
	 * @deprecated
	 */
	public function setImYahoo($im_yahoo) {
		$this->im_yahoo = $im_yahoo;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getInactivationDate() {
		return $this->inactivation_date;
	}


	/**
	 * @param mixed $inactivation_date
	 *
	 * @deprecated
	 */
	public function setInactivationDate($inactivation_date) {
		$this->inactivation_date = $inactivation_date;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getInstitution() {
		return $this->institution;
	}


	/**
	 * @param mixed $institution
	 *
	 * @deprecated
	 */
	public function setInstitution($institution) {
		$this->institution = $institution;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getIsSelfRegistered() {
		return $this->is_self_registered;
	}


	/**
	 * @param mixed $is_self_registered
	 *
	 * @deprecated
	 */
	public function setIsSelfRegistered($is_self_registered) {
		$this->is_self_registered = $is_self_registered;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getLastLogin() {
		return $this->last_login;
	}


	/**
	 * @param mixed $last_login
	 *
	 * @deprecated
	 */
	public function setLastLogin($last_login) {
		$this->last_login = $last_login;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getLastPasswordChange() {
		return $this->last_password_change;
	}


	/**
	 * @param mixed $last_password_change
	 *
	 * @deprecated
	 */
	public function setLastPasswordChange($last_password_change) {
		$this->last_password_change = $last_password_change;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getLastUpdate() {
		return $this->last_update;
	}


	/**
	 * @param mixed $last_update
	 *
	 * @deprecated
	 */
	public function setLastUpdate($last_update) {
		$this->last_update = $last_update;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getLastVisited() {
		return $this->last_visited;
	}


	/**
	 * @param mixed $last_visited
	 *
	 * @deprecated
	 */
	public function setLastVisited($last_visited) {
		$this->last_visited = $last_visited;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getLastname() {
		return $this->lastname;
	}


	/**
	 * @param mixed $lastname
	 *
	 * @deprecated
	 */
	public function setLastname($lastname) {
		$this->lastname = $lastname;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getLatitude() {
		return $this->latitude;
	}


	/**
	 * @param mixed $latitude
	 *
	 * @deprecated
	 */
	public function setLatitude($latitude) {
		$this->latitude = $latitude;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getLocZoom() {
		return $this->loc_zoom;
	}


	/**
	 * @param mixed $loc_zoom
	 *
	 * @deprecated
	 */
	public function setLocZoom($loc_zoom) {
		$this->loc_zoom = $loc_zoom;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getLogin() {
		return $this->login;
	}


	/**
	 * @param mixed $login
	 *
	 * @deprecated
	 */
	public function setLogin($login) {
		$this->login = $login;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getLoginAttempts() {
		return $this->login_attempts;
	}


	/**
	 * @param mixed $login_attempts
	 *
	 * @deprecated
	 */
	public function setLoginAttempts($login_attempts) {
		$this->login_attempts = $login_attempts;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getLongitude() {
		return $this->longitude;
	}


	/**
	 * @param mixed $longitude
	 *
	 * @deprecated
	 */
	public function setLongitude($longitude) {
		$this->longitude = $longitude;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getMatriculation() {
		return $this->matriculation;
	}


	/**
	 * @param mixed $matriculation
	 *
	 * @deprecated
	 */
	public function setMatriculation($matriculation) {
		$this->matriculation = $matriculation;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getPasswd() {
		return $this->passwd;
	}


	/**
	 * @param mixed $passwd
	 *
	 * @deprecated
	 */
	public function setPasswd($passwd) {
		$this->passwd = $passwd;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getPhoneHome() {
		return $this->phone_home;
	}


	/**
	 * @param mixed $phone_home
	 *
	 * @deprecated
	 */
	public function setPhoneHome($phone_home) {
		$this->phone_home = $phone_home;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getPhoneMobile() {
		return $this->phone_mobile;
	}


	/**
	 * @param mixed $phone_mobile
	 *
	 * @deprecated
	 */
	public function setPhoneMobile($phone_mobile) {
		$this->phone_mobile = $phone_mobile;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getPhoneOffice() {
		return $this->phone_office;
	}


	/**
	 * @param mixed $phone_office
	 *
	 * @deprecated
	 */
	public function setPhoneOffice($phone_office) {
		$this->phone_office = $phone_office;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getProfileIncomplete() {
		return $this->profile_incomplete;
	}


	/**
	 * @param mixed $profile_incomplete
	 *
	 * @deprecated
	 */
	public function setProfileIncomplete($profile_incomplete) {
		$this->profile_incomplete = $profile_incomplete;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getReferralComment() {
		return $this->referral_comment;
	}


	/**
	 * @param mixed $referral_comment
	 *
	 * @deprecated
	 */
	public function setReferralComment($referral_comment) {
		$this->referral_comment = $referral_comment;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getRegHash() {
		return $this->reg_hash;
	}


	/**
	 * @param mixed $reg_hash
	 *
	 * @deprecated
	 */
	public function setRegHash($reg_hash) {
		$this->reg_hash = $reg_hash;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getSelCountry() {
		return $this->sel_country;
	}


	/**
	 * @param mixed $sel_country
	 *
	 * @deprecated
	 */
	public function setSelCountry($sel_country) {
		$this->sel_country = $sel_country;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getStreet() {
		return $this->street;
	}


	/**
	 * @param mixed $street
	 *
	 * @deprecated
	 */
	public function setStreet($street) {
		$this->street = $street;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getTimeLimitFrom() {
		return $this->time_limit_from;
	}


	/**
	 * @param mixed $time_limit_from
	 *
	 * @deprecated
	 */
	public function setTimeLimitFrom($time_limit_from) {
		$this->time_limit_from = $time_limit_from;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getTimeLimitMessage() {
		return $this->time_limit_message;
	}


	/**
	 * @param mixed $time_limit_message
	 *
	 * @deprecated
	 */
	public function setTimeLimitMessage($time_limit_message) {
		$this->time_limit_message = $time_limit_message;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getTimeLimitOwner() {
		return $this->time_limit_owner;
	}


	/**
	 * @param mixed $time_limit_owner
	 *
	 * @deprecated
	 */
	public function setTimeLimitOwner($time_limit_owner) {
		$this->time_limit_owner = $time_limit_owner;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getTimeLimitUnlimited() {
		return $this->time_limit_unlimited;
	}


	/**
	 * @param mixed $time_limit_unlimited
	 *
	 * @deprecated
	 */
	public function setTimeLimitUnlimited($time_limit_unlimited) {
		$this->time_limit_unlimited = $time_limit_unlimited;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getTimeLimitUntil() {
		return $this->time_limit_until;
	}


	/**
	 * @param mixed $time_limit_until
	 *
	 * @deprecated
	 */
	public function setTimeLimitUntil($time_limit_until) {
		$this->time_limit_until = $time_limit_until;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getTitle() {
		return $this->title;
	}


	/**
	 * @param mixed $title
	 *
	 * @deprecated
	 */
	public function setTitle($title) {
		$this->title = $title;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getUsrId() {
		return $this->usr_id;
	}


	/**
	 * @param mixed $usr_id
	 *
	 * @deprecated
	 */
	public function setUsrId($usr_id) {
		$this->usr_id = $usr_id;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getZipcode() {
		return $this->zipcode;
	}


	/**
	 * @param mixed $zipcode
	 *
	 * @deprecated
	 */
	public function setZipcode($zipcode) {
		$this->zipcode = $zipcode;
	}


	/**
	 * @return boolean
	 *
	 * @deprecated
	 */
	public function isArSafeRead() {
		return $this->ar_safe_read;
	}


	/**
	 * @param boolean $ar_safe_read
	 *
	 * @deprecated
	 */
	public function setArSafeRead($ar_safe_read) {
		$this->ar_safe_read = $ar_safe_read;
	}


	/**
	 * @return int
	 *
	 * @deprecated
	 */
	public function getKey() {
		return $this->key;
	}


	/**
	 * @param int $key
	 *
	 * @deprecated
	 */
	public function setKey($key) {
		$this->key = $key;
	}
}
