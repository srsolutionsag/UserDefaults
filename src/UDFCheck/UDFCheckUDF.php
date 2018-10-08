<?php

namespace srag\Plugins\UserDefaults\UDFCheck;

use ilObjUser;

/**
 * Class UDFCheckUDF
 *
 * @package srag\Plugins\UserDefaults\UDFCheck
 */
class UDFCheckUDF extends UDFCheck {

	const TABLE_NAME = 'usr_def_checks_udf';


	/**
	 * @inheritdoc
	 */
	public function getFieldCategory() {
		return self::FIELD_CATEGORY_UDF;
	}


	/**
	 * @inheritdoc
	 */
	protected function getFieldValue(ilObjUser $user) {
		$user->readUserDefinedFields();

		return explode(self::CHECK_SPLIT, $user->user_defined_data['f_' . $this->getFieldKey()]);
	}
}
