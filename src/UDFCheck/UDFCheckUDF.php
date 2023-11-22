<?php

namespace srag\Plugins\UserDefaults\UDFCheck;

use ilObjUser;
use ilUserDefinedFields;

class UDFCheckUDF extends UDFCheck {

	const TABLE_NAME = 'usr_def_checks_udf';
	const FIELD_CATEGORY = 2;
	/**
	 * @var array|null
	 */
	protected static ?array $all_definitions_of_category = NULL;

	protected static function getDefinitionsOfCategory(): array
    {
		if (self::$all_definitions_of_category !== NULL) {
			return self::$all_definitions_of_category;
		}

		self::$all_definitions_of_category = [];

		$user_defined_fields = ilUserDefinedFields::_getInstance();
		foreach ($user_defined_fields->getDefinitions() as $field) {
			$udf_field = array();

			if (!self::isCustomUserFieldsHelperAvailable()) {
				if (!in_array($field['field_type'], array( UDF_TYPE_TEXT, UDF_TYPE_SELECT ))) {
					continue;
				}
			}

			$udf_field["txt"] = $field["field_name"];
			$udf_field["field_category"] = self::FIELD_CATEGORY;
			$udf_field["field_key"] = $field["field_id"];
			$udf_field["field_type"] = $field["field_type"];
			$udf_field["field_values"] = $field["field_values"];

			self::$all_definitions_of_category [] = $udf_field;
		}

		return self::$all_definitions_of_category;
	}

	protected function getFieldValue(ilObjUser $user): array
    {
		$user->readUserDefinedFields();
		return explode(self::CHECK_SPLIT, $user->user_defined_data['f_' . $this->getFieldKey()]);
	}
}
