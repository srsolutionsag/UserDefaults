<?php

namespace srag\Plugins\UserDefaults\UDFCheck;

use ilObjUser;

/**
 * Class UDFCheckUser
 *
 * @package srag\Plugins\UserDefaults\UDFCheck
 */
class UDFCheckUser extends UDFCheck {

	const TABLE_NAME = 'usr_def_checks_user';


	/**
	 * @inheritdoc
	 */
	public function getFieldCategory() {
		return self::FIELD_CATEGORY_USR;
	}


	/**
	 * @inheritdoc
	 */
	protected function getFieldValue(ilObjUser $user) {
		return [
			$this->getFieldKey() => trim($this->getUserFieldValue($user, $this->getFieldKey()))
		];
	}


	/**
	 * @param ilObjUser $user
	 * @param string    $field_name
	 *
	 * @return string
	 */
	protected function getUserFieldValue(ilObjUser $user, $field_name) {
		switch ($field_name) {
			case 'gender':
				return $user->getGender();

			case 'lastname':
				return $user->getLastname();

			case 'firstname':
				return $user->getFirstname();

			case 'login':
				return $user->getLogin();

			case 'title':
				return $user->getTitle();

			case 'institution':
				return $user->getInstitution();

			case 'department':
				return $user->getDepartment();

			case 'street':
				return $user->getStreet();

			case 'zipcode':
				return $user->getZipcode();

			case 'city':
				return $user->getCity();

			case 'country':
				return $user->getCountry();

			case 'sel_country':
				return $user->getSelectedCountry();

			case 'email':
				return $user->getEmail();

			case 'second_email':
				return $user->getSecondEmail();

			case 'hobby':
				return $user->getHobby();

			case 'org_units':
				return $user->getOrgUnitsRepresentation();

			case 'matriculation':
				return $user->getMatriculation();

			case 'interests_general':
				return $user->getGeneralInterestsAsText();

			case 'interests_help_offered':
				return $user->getOfferingHelpAsText();

			case 'interests_help_looking':
				return $user->getLookingForHelpAsText();

			default:
				return '';
		}
	}
}
