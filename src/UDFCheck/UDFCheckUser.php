<?php

namespace srag\Plugins\UserDefaults\UDFCheck;

use arField;
use ilObjUser;
use ilUserSearchOptions;

class UDFCheckUser extends UDFCheck {

	const TABLE_NAME = 'usr_def_checks_user';
	const FIELD_CATEGORY = 1;
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

		foreach (ilUserSearchOptions::_getSearchableFieldsInfo(true) as $field) {
			$usr_field = array();

			if (array_key_exists('type', $field) && !in_array($field['type'], array( ilUserSearchOptions::FIELD_TYPE_TEXT,  ilUserSearchOptions::FIELD_TYPE_SELECT,  ilUserSearchOptions::FIELD_TYPE_MULTI ))) {
				continue;
			}

			$usr_field["txt"] = $field["lang"];
			$usr_field["field_category"] = self::FIELD_CATEGORY;
			$usr_field["field_key"] = $field["db"];
			$usr_field["field_type"] = $field["type"];
			$usr_field["field_values"] = $field["values"];

			self::$all_definitions_of_category[] = $usr_field;
		}

		return self::$all_definitions_of_category;
	}


	/**
	 * @inheritdoc
	 */
	protected function getFieldValue(ilObjUser $user): array
    {
		return [
			$this->getFieldKey() => trim($this->getUserFieldValue($user, $this->getFieldKey()))
		];
	}


	protected function getUserFieldValue(ilObjUser $user, string $field_name): string
    {
        return match ($field_name) {
            'gender' => $user->getGender(),
            'lastname' => $user->getLastname(),
            'firstname' => $user->getFirstname(),
            'login' => $user->getLogin(),
            'title' => $user->getTitle(),
            'institution' => $user->getInstitution(),
            'department' => $user->getDepartment(),
            'street' => $user->getStreet(),
            'zipcode' => $user->getZipcode(),
            'city' => $user->getCity(),
            'country' => $user->getCountry(),
            'sel_country' => $user->getSelectedCountry(),
            'email' => $user->getEmail(),
            'second_email' => $user->getSecondEmail(),
            'hobby' => $user->getHobby(),
            'org_units' => $user->getOrgUnitsRepresentation(),
            'matriculation' => $user->getMatriculation(),
            'interests_general' => $user->getGeneralInterestsAsText(),
            'interests_help_offered' => $user->getOfferingHelpAsText(),
            'interests_help_looking' => $user->getLookingForHelpAsText(),
            default => '',
        };
	}
}
