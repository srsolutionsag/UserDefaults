<?php

namespace srag\Plugins\UserDefaults\UDFCheck;

use ILIAS\User\Context;
use ILIAS\User\LocalDIC;
use ILIAS\User\Profile\Fields\Custom\Custom;
use ILIAS\User\Profile\Profile;
use ilMultiSelectInputGUI;
use ilObjUser;
use ilSelectInputGUI;
use ilTextAreaInputGUI;
use ilTextInputGUI;
use ilUserSearchOptions;

class UDFCheckUDF extends UDFCheck
{
    public const TABLE_NAME = 'usr_def_checks_udf';
    public const FIELD_CATEGORY = 2;
    protected static ?array $all_definitions_of_category = null;

    protected static function getDefinitionsOfCategory(): array
    {
        if (self::$all_definitions_of_category !== null) {
            return self::$all_definitions_of_category;
        }

        global $DIC;

        self::$all_definitions_of_category = [];

        $lng = $DIC->language();
        // Not $DIC['user']: that one is bound to a logged in user, while this
        // code also runs from the cron context. getProfile() only delegates here.
        $profile = LocalDIC::dic()[Profile::class];

        foreach ($profile->getFields() as $field) {
            if (!$field->isCustom()) {
                continue;
            }

            $definition = $field->getDefinition();
            if ($definition instanceof Custom && $definition->isUnspecific()) {
                continue; // custom field without a type yet, nothing to check against
            }

            // Derive the type from the legacy input a field builds, the same way
            // ilUserSearchOptions::__appendUserDefinedFields() does. Types
            // contributed by other plugins end up in the default branch.
            $input = $field->getLegacyInput($lng, Context::UserAdministration);
            $type_and_values = match ($input::class) {
                ilTextInputGUI::class,
                ilTextAreaInputGUI::class => [ilUserSearchOptions::FIELD_TYPE_UDF_TEXT, []],
                ilSelectInputGUI::class,
                ilMultiSelectInputGUI::class => [
                    ilUserSearchOptions::FIELD_TYPE_UDF_SELECT,
                    self::optionsToValues($input->getOptions())
                ],
                default => null,
            };

            if ($type_and_values === null) {
                continue;
            }

            self::$all_definitions_of_category[] = [
                'txt' => $field->getLabel($lng),
                'field_category' => self::FIELD_CATEGORY,
                'field_key' => $field->getIdentifier(),
                'field_type' => $type_and_values[0],
                'field_values' => $type_and_values[1],
            ];
        }

        return self::$all_definitions_of_category;
    }

    /**
     * @param array<string, string> $options
     * @return list<string>
     */
    private static function optionsToValues(array $options): array
    {
        unset($options['']); // the "please select" entry a single select prepends

        return array_values($options);
    }

    protected function getFieldValue(ilObjUser $user): array
    {
        $value = $user->getProfileData()->getAdditionalFieldByIdentifier($this->getFieldKey());

        if (is_array($value)) {
            return $value;
        }

        return ($value === null || $value === '') ? [] : [$value];
    }
}
