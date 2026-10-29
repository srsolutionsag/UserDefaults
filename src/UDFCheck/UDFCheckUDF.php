<?php

namespace srag\Plugins\UserDefaults\UDFCheck;

use ILIAS\User\Context;
use ILIAS\User\LocalDIC;
use ILIAS\User\Profile\Fields\Custom\Custom;
use ILIAS\User\Profile\Profile;
use ilFormPropertyGUI;
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
    /**
     * Custom fields of the CascadingSelect plugin (Leifos\CascadingSelect). Its
     * value is one selection per level, joined with the same ' → ' this plugin
     * uses as CHECK_SPLIT, so each level is compared against its own part of
     * the check value.
     */
    public const FIELD_TYPE_UDF_CASCADE = 100;
    private const CASCADE_INPUT_CLASS = 'Leifos\\CascadingSelect\\UI\\FieldInputGUI';
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
                default => is_a($input, self::CASCADE_INPUT_CLASS)
                    ? [self::FIELD_TYPE_UDF_CASCADE, []]
                    : null,
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
                'field_multi' => $input instanceof ilMultiSelectInputGUI,
                'field_levels' => $type_and_values[0] === self::FIELD_TYPE_UDF_CASCADE
                    ? self::cascadeLevels($field->getIdentifier())
                    : [],
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

    /**
     * The level names of a CascadingSelect field. The plugin keeps them in the
     * JSON it stores as the field's additional data, which Profile\Fields\Custom
     * offers no getter for.
     *
     * @return list<string>
     */
    private static function cascadeLevels(string $field_id): array
    {
        global $DIC;

        $db = $DIC->database();
        $row = $db->fetchAssoc($db->queryF(
            'SELECT field_values FROM udf_definition WHERE field_id = %s',
            [\ilDBConstants::T_TEXT],
            [$field_id]
        ));

        $data = json_decode((string) ($row['field_values'] ?? ''), true);

        return array_values(array_map(
            static fn(array $column): string => (string) ($column['name'] ?? ''),
            array_filter((array) ($data['columns'] ?? []), 'is_array')
        ));
    }

    /**
     * The CascadingSelect input itself, for picking a check value level by level.
     */
    public function getCascadeInput(): ?ilFormPropertyGUI
    {
        global $DIC;

        if (!$this->isCascade()) {
            return null;
        }

        foreach (LocalDIC::dic()[Profile::class]->getFields() as $field) {
            if ($field->getIdentifier() === (string) $this->getFieldKey()) {
                return $field->getLegacyInput($DIC->language(), Context::UserAdministration);
            }
        }

        return null;
    }

    public function isCascade(): bool
    {
        return (int) ($this->getDefinition()['field_type'] ?? 0) === self::FIELD_TYPE_UDF_CASCADE;
    }

    #[\Override]
    protected function isPositional(): bool
    {
        return $this->isCascade();
    }

    #[\Override]
    protected function isMultiValued(): bool
    {
        return (bool) ($this->getDefinition()['field_multi'] ?? false);
    }

    protected function getFieldValue(ilObjUser $user): array
    {
        $value = $user->getProfileData()->getAdditionalFieldByIdentifier($this->getFieldKey());

        if ($this->isCascade()) {
            return explode(self::CHECK_SPLIT, (string) (is_array($value) ? reset($value) : $value));
        }

        if (is_array($value)) {
            return $value;
        }

        return ($value === null || $value === '') ? [] : [$value];
    }
}
