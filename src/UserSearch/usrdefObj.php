<?php

namespace srag\Plugins\UserDefaults\UserSearch;

use ActiveRecord;
use ilUserDefaultsPlugin;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

/**
 *
 * @deprecated TODO: Remove this class
 */
class usrdefObj extends ActiveRecord
{
    use UserDefaultsTrait;

    /**
     * @var string
     *
     * @deprecated
     */
    public const TABLE_NAME = "object_data";
    /**
     * @var string
     *
     * @deprecated
     */
    public const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;

    /**
     * @deprecated
     */
    public function getConnectorContainerName(): string
    {
        return self::TABLE_NAME;
    }

    /**
     * @deprecated
     */
    public static function returnDbTableName(): string
    {
        return self::TABLE_NAME;
    }

    /**
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     4
     * @con_is_notnull true
     * @con_is_primary true
     * @con_is_unique  true
     *
     * @deprecated
     */
    protected int $obj_id;
    /**
     * @con_has_field true
     * @con_fieldtype text
     * @con_length    4
     *
     * @deprecated
     */
    protected string $type;
    /**
     * @con_has_field true
     * @con_fieldtype text
     * @con_length    255
     *
     * @deprecated
     */
    protected string $title;
    /**
     * @con_has_field true
     * @con_fieldtype text
     * @con_length    128
     *
     * @deprecated
     */
    protected string $description;
    /**
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     4
     * @con_is_notnull true
     *
     * @deprecated
     */
    protected int $owner;
    /**
     * @var
     *
     * @con_has_field true
     * @con_fieldtype
     * @con_length
     *
     * @deprecated
     */
    protected $create_date;
    /**
     * @var
     *
     * @con_has_field true
     * @con_fieldtype
     * @con_length
     *
     * @deprecated
     */
    protected $last_update;
    /**
     * @var
     *
     * @con_has_field true
     * @con_fieldtype text
     * @con_length    50
     *
     * @deprecated
     */
    protected $import_id;

    /**
     * @deprecated
     */
    public function getObjId(): int
    {
        return $this->obj_id;
    }

    /**
     * @deprecated
     */
    public function setObjId(int $obj_id): void
    {
        $this->obj_id = $obj_id;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     *
     * @deprecated
     */
    public function setType(string $type): void
    {
        $this->type = $type;
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
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     *
     * @deprecated
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getOwner(): int
    {
        return $this->owner;
    }

    /**
     * @param mixed $owner
     *
     * @deprecated
     */
    public function setOwner(int $owner): void
    {
        $this->owner = $owner;
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
    public function getLastUpdate()
    {
        return $this->last_update;
    }

    /**
     * @deprecated
     */
    public function setLastUpdate(mixed $last_update): void
    {
        $this->last_update = $last_update;
    }

    /**
     * @return mixed
     *
     * @deprecated
     */
    public function getImportId()
    {
        return $this->import_id;
    }

    /**
     * @deprecated
     */
    public function setImportId(mixed $import_id): void
    {
        $this->import_id = $import_id;
    }
}
