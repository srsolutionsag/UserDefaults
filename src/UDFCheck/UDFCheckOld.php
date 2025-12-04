<?php

namespace srag\Plugins\UserDefaults\UDFCheck;

use ActiveRecord;
use ilUserDefaultsPlugin;
use srag\Plugins\UserDefaults\Config\UserDefaultsConfig;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

class UDFCheckOld extends ActiveRecord
{
    /**
     * @var string
     *
     * @deprecated
     */
    public const TABLE_NAME = 'usr_def_checks';
    /**
     * @var string
     *
     * @deprecated
     */
    public const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;

    /**
     * @deprecated
     */
    #[\Override]
    public function getConnectorContainerName(): string
    {
        return static::TABLE_NAME;
    }

    /**
     * @deprecated
     */
    public static function returnDbTableName(): string
    {
        return static::TABLE_NAME;
    }

    /**
     *
     * @con_is_primary true
     * @con_is_unique  true
     * @con_sequence   true
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     8
     * @deprecated
     */
    protected int $id = 0;
    /**
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     8
     * @deprecated
     */
    protected int $parent_id = 0;
    /**
     * @var string
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_length     256
     *
     * @deprecated
     *
     */
    protected $field_key = 1;
    /**
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     1
     * @db_is_notnull  true
     * @deprecated
     */
    protected int $field_category = UDFCheckUser::FIELD_CATEGORY;
    /**
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_length     256
     * @deprecated
     */
    protected string $check_value = '';
    /**
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     1
     * @deprecated
     */
    protected int $operator = UDFCheck::OP_EQUALS;
    /**
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     1
     * @deprecated
     */
    protected bool $negated = false;
    /**
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     8
     * @deprecated
     */
    protected int $owner = 6;
    /**
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     8
     * @deprecated
     */
    protected int $status = UDFCheck::STATUS_ACTIVE;
    /**
     *
     * @db_has_field        true
     * @db_fieldtype        timestamp
     * @db_is_notnull       true
     * @deprecated
     */
    protected int $create_date;
    /**
     *
     * @db_has_field        true
     * @db_fieldtype        timestamp
     * @db_is_notnull       true
     * @deprecated
     */
    protected int $update_date;

    /**
     * @deprecated
     */
    #[\Override]
    public function update(): void
    {
        $this->setOwner(self::dic()->user()->getId());
        $this->setUpdateDate(time());
        parent::update();
    }

    /**
     * @deprecated
     */
    #[\Override]
    public function create(): void
    {
        $this->setOwner(self::dic()->user()->getId());
        $this->setUpdateDate(time());
        $this->setCreateDate(time());
        parent::create();
    }

    /**
     * @deprecated
     */
    public function setCheckValue(string $check_value): void
    {
        $this->check_value = $check_value;
    }

    /**
     * @deprecated
     */
    public function getCheckValue(): string
    {
        return $this->check_value;
    }

    /**
     * @param string $field_key
     *
     * @deprecated
     */
    public function setFieldKey($field_key): void
    {
        $this->field_key = $field_key;
    }

    /**
     * @return string
     *
     * @deprecated
     */
    public function getFieldKey()
    {
        return $this->field_key;
    }

    /**
     * @deprecated
     */
    public function getFieldCategory(): int
    {
        return $this->field_category;
    }

    /**
     * @deprecated
     */
    public function setFieldCategory(int $field_category): void
    {
        $this->field_category = $field_category;
    }

    /**
     * @deprecated
     */
    public function setOperator(int $operator): void
    {
        $this->operator = $operator;
    }

    /**
     * @deprecated
     */
    public function getOperator(): int
    {
        return $this->operator;
    }

    /**
     * @deprecated
     */
    public function setCreateDate(int $create_date): void
    {
        $this->create_date = $create_date;
    }

    /**
     * @deprecated
     */
    public function getCreateDate(): int
    {
        return $this->create_date;
    }

    /**
     * @deprecated
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @deprecated
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @deprecated
     */
    public function setOwner(int $owner): void
    {
        $this->owner = $owner;
    }

    /**
     * @deprecated
     */
    public function getOwner(): int
    {
        return $this->owner;
    }

    /**
     * @deprecated
     */
    public function setUpdateDate(int $update_date): void
    {
        $this->update_date = $update_date;
    }

    /**
     * @deprecated
     */
    public function getUpdateDate(): int
    {
        return $this->update_date;
    }

    /**
     * @deprecated
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @deprecated
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @deprecated
     */
    public function setParentId(int $parent_id): void
    {
        $this->parent_id = $parent_id;
    }

    /**
     * @deprecated
     */
    public function getParentId(): int
    {
        return $this->parent_id;
    }

    /**
     * @deprecated
     */
    public function isNegated(): bool
    {
        return $this->negated;
    }

    /**
     * @deprecated
     */
    public function setNegated(bool $negated): void
    {
        $this->negated = $negated;
    }

    /**
     * @param $field_name
     *
     * @return mixed|null|string
     *
     * @deprecated
     */
    public function sleep($field_name): ?string
    {
        return match ($field_name) {
            'create_date', 'update_date' => date(UserDefaultsConfig::SQL_DATE_FORMAT, $this->{$field_name}),
            default => null,
        };
    }

    /**
     * @param $field_name
     * @param $field_value
     *
     * @return int|false|null
     *
     * @deprecated
     */
    public function wakeUp($field_name, $field_value): int|false|null
    {
        return match ($field_name) {
            'create_date', 'update_date' => strtotime((string) $field_value),
            default => null,
        };
    }
}
