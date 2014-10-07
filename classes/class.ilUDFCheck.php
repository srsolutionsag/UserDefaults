<?php

/**
 * Class ilUDFCheck
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class ilUDFCheck extends ActiveRecord {

	const TABLE_NAME = 'usr_def_checks';
	const OP_EQUALS = 1;
	/**
	 * @var int
	 *
	 * @con_is_primary true
	 * @con_is_unique  true
	 * @con_sequence   true
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 */
	protected $id = 0;
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 */
	protected $udf_field_id = 1;
	/**
	 * @var string
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     256
	 */
	protected $check_value = 'Ja';
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     1
	 */
	protected $operator = self::OP_EQUALS;


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


	/**
	 * @param string $check_value
	 */
	public function setCheckValue($check_value) {
		$this->check_value = $check_value;
	}


	/**
	 * @return string
	 */
	public function getCheckValue() {
		return $this->check_value;
	}


	/**
	 * @param int $udf_field_id
	 */
	public function setUdfFieldId($udf_field_id) {
		$this->udf_field_id = $udf_field_id;
	}


	/**
	 * @return int
	 */
	public function getUdfFieldId() {
		return $this->udf_field_id;
	}


	/**
	 * @param int $operator
	 */
	public function setOperator($operator) {
		$this->operator = $operator;
	}


	/**
	 * @return int
	 */
	public function getOperator() {
		return $this->operator;
	}


	/**
	 * @param ilObjUser $ilUser
	 *
	 * @return bool
	 */
	public function isValid(ilObjUser $ilUser = NULL) {
		if (! $ilUser) {
			global $ilUser;
		}

		$ilUser->readUserDefinedFields();
		$value = $ilUser->user_defined_data['f_' . $this->getUdfFieldId()];

		switch ($this->getOperator()) {
			case self::OP_EQUALS:
				return $value == $this->getCheckValue();
		}
	}
}

?>
