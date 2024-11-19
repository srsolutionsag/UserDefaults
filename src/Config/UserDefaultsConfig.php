<?php

namespace srag\Plugins\UserDefaults\Config;

use ilException;
use ilUserDefaultsPlugin;
use ActiveRecord;
use arConnector;

/**
 * Class Config
 *
 * @package srag\Plugins\UserDefaults\Config
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class UserDefaultsConfig extends ActiveRecord
{
    protected static string $table_name = "usr_def_config";
    public const TABLE_NAME = "usr_def_config";
    public const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
    public const KEY_CATEGORY_REF_ID = "category_ref_id";
    /**
     * @var string
     */
    public const SQL_DATE_FORMAT = "Y-m-d H:i:s";
    /**
     * @var int
     */
    public const TYPE_BOOLEAN = 4;
    /**
     * @var int
     */
    public const TYPE_DATETIME = 6;
    /**
     * @var int
     */
    public const TYPE_DOUBLE = 3;
    /**
     * @var int
     */
    public const TYPE_INTEGER = 2;
    /**
     * @var int
     */
    public const TYPE_JSON = 7;
    /**
     * @var int
     */
    public const TYPE_STRING = 1;
    /**
     * @var int
     */
    public const TYPE_TIMESTAMP = 5;

    /**
     * @var string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_length      100
     * @con_is_notnull  true
     * @con_is_primary  true
     */
    protected ?string $name = "";
    /**
     * @var mixed
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_is_notnull  false
     */
    protected $value = null;

    public function __construct(?string $primary_name_value = null, ?arConnector $connector = null)
    {
        parent::__construct($primary_name_value, $connector);
    }

    public static function returnDbTableName(): string
    {
        return self::getTableName();
    }

    public function getConnectorContainerName(): string
    {
        return self::getTableName();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int|string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string|int $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    public function sleep($field_name)
    {
        $field_value = $this->{$field_name};

        return parent::sleep($field_name);
    }

    public function wakeUp(/*string*/ $field_name, $field_value)
    {
        return parent::wakeUp($field_name, $field_value);
    }

    private static array $fields = [
        self::KEY_CATEGORY_REF_ID => self::TYPE_INTEGER
    ];

    public static function getTableName(): string
    {
        return self::$table_name;
    }

    public static function setTableName(string $table_name): void
    {
        self::$table_name = $table_name;
    }

    /**
     * @throws ilException
     */
    public static function getField(string $field): int
    {
        if (array_key_exists($field, self::$fields)) {
            return self::$fields[$field];
        }

        throw new ilException("UserDefaults configuration field '$field' not found");
    }
}
