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
class usrdefUser extends ActiveRecord
{
    use UserDefaultsTrait;

    public $approve_date;
    public $create_date;
    public $last_login;
    public $last_visited;
    public $latitude;
    public $longitude;
    public $key;
    /**
     * @var string
     *
     * @deprecated
     */
    public const TABLE_NAME = "usr_data";
    /**
     * @var string
     *
     * @deprecated
     */
    public const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;

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
        throw new ilException('ActiveReacord Class ' . self::class . ' is not allowed to ' . __METHOD__ . ' objects');
    }

    /**
     * @deprecated
     */
    public function update(): void
    {
        throw new ilException('ActiveReacord Class ' . self::class . ' is not allowed to ' . __METHOD__ . ' objects');
    }

    /**
     * @deprecated
     */
    public function delete(): void
    {
        throw new ilException('ActiveReacord Class ' . self::class . ' is not allowed to ' . __METHOD__ . ' objects');
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
    protected ?int $usr_id = null;
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
    protected ?int $time_limit_from = null;
    /**
     * @var
     *
     * @con_has_field true
     * @con_fieldtype integer
     * @con_length    4
     *
     * @deprecated
     */
    protected ?int $time_limit_until = null;
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
    protected ?string $agree_date = null;
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
    protected ?string $feed_hash = null;
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
    protected ?string $reg_hash = null;
    /**
     * @var
     *
     * @con_has_field true
     * @con_fieldtype text
     *
     * @deprecated
     */
    protected ?string $birthday = null;
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
    protected ?string $inactivation_date = null;
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
    public function getActive(): int
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     *
     * @deprecated
     */
    public function setActive(int $active): void
    {
        $this->active = $active;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getAgreeDate(): ?string
    {
        return $this->agree_date;
    }

    /**
     * @param mixed $agree_date
     *
     * @deprecated
     */
    public function setAgreeDate(?string $agree_date): void
    {
        $this->agree_date = $agree_date;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getApproveDate()
    {
        return $this->approve_date;
    }

    /**
     * @deprecated
     */
    public function setApproveDate(mixed $approve_date): void
    {
        $this->approve_date = $approve_date;
    }

    /**
     * @return usrdefObj
     *
     * @deprecated
     */
    public function getusrdefObj(): usrdefObj
    {
        return $this->usrdefObj;
    }

    /**
     * @param usrdefObj $usrdefObj
     *
     * @deprecated
     */
    public function setusrdefObj(usrdefObj $usrdefObj): void
    {
        $this->usrdefObj = $usrdefObj;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getAuthMode(): string
    {
        return $this->auth_mode;
    }

    /**
     * @param mixed $auth_mode
     *
     * @deprecated
     */
    public function setAuthMode(string $auth_mode): void
    {
        $this->auth_mode = $auth_mode;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getBirthday(): ?string
    {
        return $this->birthday;
    }

    /**
     * @param mixed $birthday
     *
     * @deprecated
     */
    public function setBirthday(?string $birthday): void
    {
        $this->birthday = $birthday;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     *
     * @deprecated
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getClientIp(): string
    {
        return $this->client_ip;
    }

    /**
     * @param mixed $client_ip
     *
     * @deprecated
     */
    public function setClientIp(string $client_ip): void
    {
        $this->client_ip = $client_ip;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     *
     * @deprecated
     */
    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getCreateDate()
    {
        return $this->create_date;
    }

    /**
     * @deprecated
     */
    public function setCreateDate(mixed $create_date): void
    {
        $this->create_date = $create_date;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getDelicious(): string
    {
        return $this->delicious;
    }

    /**
     * @param mixed $delicious
     *
     * @deprecated
     */
    public function setDelicious(string $delicious): void
    {
        $this->delicious = $delicious;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getDepartment(): string
    {
        return $this->department;
    }

    /**
     * @param mixed $department
     *
     * @deprecated
     */
    public function setDepartment(string $department): void
    {
        $this->department = $department;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     *
     * @deprecated
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getExtAccount(): string
    {
        return $this->ext_account;
    }

    /**
     * @param mixed $ext_account
     *
     * @deprecated
     */
    public function setExtAccount(string $ext_account): void
    {
        $this->ext_account = $ext_account;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getFax(): string
    {
        return $this->fax;
    }

    /**
     * @param mixed $fax
     *
     * @deprecated
     */
    public function setFax(string $fax): void
    {
        $this->fax = $fax;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getFeedHash(): ?string
    {
        return $this->feed_hash;
    }

    /**
     * @param mixed $feed_hash
     *
     * @deprecated
     */
    public function setFeedHash(?string $feed_hash): void
    {
        $this->feed_hash = $feed_hash;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     *
     * @deprecated
     */
    public function setFirstname(string $firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getGender(): string
    {
        return $this->gender;
    }

    /**
     * @param mixed $gender
     *
     * @deprecated
     */
    public function setGender(string $gender): void
    {
        $this->gender = $gender;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getHobby(): string
    {
        return $this->hobby;
    }

    /**
     * @param mixed $hobby
     *
     * @deprecated
     */
    public function setHobby(string $hobby): void
    {
        $this->hobby = $hobby;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getI2passwd(): string
    {
        return $this->i2passwd;
    }

    /**
     * @param mixed $i2passwd
     *
     * @deprecated
     */
    public function setI2passwd(string $i2passwd): void
    {
        $this->i2passwd = $i2passwd;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getIlincId(): int
    {
        return $this->ilinc_id;
    }

    /**
     * @param mixed $ilinc_id
     *
     * @deprecated
     */
    public function setIlincId(int $ilinc_id): void
    {
        $this->ilinc_id = $ilinc_id;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getIlincLogin(): string
    {
        return $this->ilinc_login;
    }

    /**
     * @param mixed $ilinc_login
     *
     * @deprecated
     */
    public function setIlincLogin(string $ilinc_login): void
    {
        $this->ilinc_login = $ilinc_login;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getIlincPasswd(): string
    {
        return $this->ilinc_passwd;
    }

    /**
     * @param mixed $ilinc_passwd
     *
     * @deprecated
     */
    public function setIlincPasswd(string $ilinc_passwd): void
    {
        $this->ilinc_passwd = $ilinc_passwd;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getImAim(): string
    {
        return $this->im_aim;
    }

    /**
     * @param mixed $im_aim
     *
     * @deprecated
     */
    public function setImAim(string $im_aim): void
    {
        $this->im_aim = $im_aim;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getImIcq(): string
    {
        return $this->im_icq;
    }

    /**
     * @param mixed $im_icq
     *
     * @deprecated
     */
    public function setImIcq(string $im_icq): void
    {
        $this->im_icq = $im_icq;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getImJabber(): string
    {
        return $this->im_jabber;
    }

    /**
     * @param mixed $im_jabber
     *
     * @deprecated
     */
    public function setImJabber(string $im_jabber): void
    {
        $this->im_jabber = $im_jabber;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getImMsn(): string
    {
        return $this->im_msn;
    }

    /**
     * @param mixed $im_msn
     *
     * @deprecated
     */
    public function setImMsn(string $im_msn): void
    {
        $this->im_msn = $im_msn;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getImSkype(): string
    {
        return $this->im_skype;
    }

    /**
     * @param mixed $im_skype
     *
     * @deprecated
     */
    public function setImSkype(string $im_skype): void
    {
        $this->im_skype = $im_skype;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getImVoip(): string
    {
        return $this->im_voip;
    }

    /**
     * @param mixed $im_voip
     *
     * @deprecated
     */
    public function setImVoip(string $im_voip): void
    {
        $this->im_voip = $im_voip;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getImYahoo(): string
    {
        return $this->im_yahoo;
    }

    /**
     * @param mixed $im_yahoo
     *
     * @deprecated
     */
    public function setImYahoo(string $im_yahoo): void
    {
        $this->im_yahoo = $im_yahoo;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getInactivationDate(): ?string
    {
        return $this->inactivation_date;
    }

    /**
     * @param mixed $inactivation_date
     *
     * @deprecated
     */
    public function setInactivationDate(?string $inactivation_date): void
    {
        $this->inactivation_date = $inactivation_date;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getInstitution(): string
    {
        return $this->institution;
    }

    /**
     * @param mixed $institution
     *
     * @deprecated
     */
    public function setInstitution(string $institution): void
    {
        $this->institution = $institution;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getIsSelfRegistered(): int
    {
        return $this->is_self_registered;
    }

    /**
     * @param mixed $is_self_registered
     *
     * @deprecated
     */
    public function setIsSelfRegistered(int $is_self_registered): void
    {
        $this->is_self_registered = $is_self_registered;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getLastLogin()
    {
        return $this->last_login;
    }

    /**
     * @deprecated
     */
    public function setLastLogin(mixed $last_login): void
    {
        $this->last_login = $last_login;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getLastPasswordChange(): int
    {
        return $this->last_password_change;
    }

    /**
     * @param mixed $last_password_change
     *
     * @deprecated
     */
    public function setLastPasswordChange(int $last_password_change): void
    {
        $this->last_password_change = $last_password_change;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getLastUpdate(): string
    {
        return $this->last_update;
    }

    /**
     * @param mixed $last_update
     *
     * @deprecated
     */
    public function setLastUpdate(string $last_update): void
    {
        $this->last_update = $last_update;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getLastVisited()
    {
        return $this->last_visited;
    }

    /**
     * @deprecated
     */
    public function setLastVisited(mixed $last_visited): void
    {
        $this->last_visited = $last_visited;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     *
     * @deprecated
     */
    public function setLastname(string $lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @deprecated
     */
    public function setLatitude(mixed $latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getLocZoom(): int
    {
        return $this->loc_zoom;
    }

    /**
     * @param mixed $loc_zoom
     *
     * @deprecated
     */
    public function setLocZoom(int $loc_zoom): void
    {
        $this->loc_zoom = $loc_zoom;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @param mixed $login
     *
     * @deprecated
     */
    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getLoginAttempts(): int
    {
        return $this->login_attempts;
    }

    /**
     * @param mixed $login_attempts
     *
     * @deprecated
     */
    public function setLoginAttempts(int $login_attempts): void
    {
        $this->login_attempts = $login_attempts;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @deprecated
     */
    public function setLongitude(mixed $longitude): void
    {
        $this->longitude = $longitude;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getMatriculation(): string
    {
        return $this->matriculation;
    }

    /**
     * @param mixed $matriculation
     *
     * @deprecated
     */
    public function setMatriculation(string $matriculation): void
    {
        $this->matriculation = $matriculation;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getPasswd(): string
    {
        return $this->passwd;
    }

    /**
     * @param mixed $passwd
     *
     * @deprecated
     */
    public function setPasswd(string $passwd): void
    {
        $this->passwd = $passwd;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getPhoneHome(): string
    {
        return $this->phone_home;
    }

    /**
     * @param mixed $phone_home
     *
     * @deprecated
     */
    public function setPhoneHome(string $phone_home): void
    {
        $this->phone_home = $phone_home;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getPhoneMobile(): string
    {
        return $this->phone_mobile;
    }

    /**
     * @param mixed $phone_mobile
     *
     * @deprecated
     */
    public function setPhoneMobile(string $phone_mobile): void
    {
        $this->phone_mobile = $phone_mobile;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getPhoneOffice(): string
    {
        return $this->phone_office;
    }

    /**
     * @param mixed $phone_office
     *
     * @deprecated
     */
    public function setPhoneOffice(string $phone_office): void
    {
        $this->phone_office = $phone_office;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getProfileIncomplete(): int
    {
        return $this->profile_incomplete;
    }

    /**
     * @param mixed $profile_incomplete
     *
     * @deprecated
     */
    public function setProfileIncomplete(int $profile_incomplete): void
    {
        $this->profile_incomplete = $profile_incomplete;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getReferralComment(): string
    {
        return $this->referral_comment;
    }

    /**
     * @param mixed $referral_comment
     *
     * @deprecated
     */
    public function setReferralComment(string $referral_comment): void
    {
        $this->referral_comment = $referral_comment;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getRegHash(): ?string
    {
        return $this->reg_hash;
    }

    /**
     * @param mixed $reg_hash
     *
     * @deprecated
     */
    public function setRegHash(?string $reg_hash): void
    {
        $this->reg_hash = $reg_hash;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getSelCountry(): string
    {
        return $this->sel_country;
    }

    /**
     * @param mixed $sel_country
     *
     * @deprecated
     */
    public function setSelCountry(string $sel_country): void
    {
        $this->sel_country = $sel_country;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * @param mixed $street
     *
     * @deprecated
     */
    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getTimeLimitFrom(): ?int
    {
        return $this->time_limit_from;
    }

    /**
     * @param mixed $time_limit_from
     *
     * @deprecated
     */
    public function setTimeLimitFrom(?int $time_limit_from): void
    {
        $this->time_limit_from = $time_limit_from;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getTimeLimitMessage(): int
    {
        return $this->time_limit_message;
    }

    /**
     * @param mixed $time_limit_message
     *
     * @deprecated
     */
    public function setTimeLimitMessage(int $time_limit_message): void
    {
        $this->time_limit_message = $time_limit_message;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getTimeLimitOwner(): int
    {
        return $this->time_limit_owner;
    }

    /**
     * @param mixed $time_limit_owner
     *
     * @deprecated
     */
    public function setTimeLimitOwner(int $time_limit_owner): void
    {
        $this->time_limit_owner = $time_limit_owner;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getTimeLimitUnlimited(): int
    {
        return $this->time_limit_unlimited;
    }

    /**
     * @param mixed $time_limit_unlimited
     *
     * @deprecated
     */
    public function setTimeLimitUnlimited(int $time_limit_unlimited): void
    {
        $this->time_limit_unlimited = $time_limit_unlimited;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getTimeLimitUntil(): ?int
    {
        return $this->time_limit_until;
    }

    /**
     * @param mixed $time_limit_until
     *
     * @deprecated
     */
    public function setTimeLimitUntil(?int $time_limit_until): void
    {
        $this->time_limit_until = $time_limit_until;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     *
     * @deprecated
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getUsrId(): ?int
    {
        return $this->usr_id;
    }

    /**
     * @param mixed $usr_id
     *
     * @deprecated
     */
    public function setUsrId(?int $usr_id): void
    {
        $this->usr_id = $usr_id;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getZipcode(): string
    {
        return $this->zipcode;
    }

    /**
     * @param mixed $zipcode
     *
     * @deprecated
     */
    public function setZipcode(string $zipcode): void
    {
        $this->zipcode = $zipcode;
    }

    /**
     * @return boolean
     *
     * @deprecated
     */
    public function isArSafeRead(): bool
    {
        return $this->ar_safe_read;
    }

    /**
     * @param boolean $ar_safe_read
     *
     * @deprecated
     */
    public function setArSafeRead(bool $ar_safe_read): void
    {
        $this->ar_safe_read = $ar_safe_read;
    }

    /**
     * @return int
     *
     * @deprecated
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param int $key
     *
     * @deprecated
     */
    public function setKey($key): void
    {
        $this->key = $key;
    }
}
