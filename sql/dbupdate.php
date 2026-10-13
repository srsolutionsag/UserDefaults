<#1>
<?php

\srag\Plugins\UserDefaults\UserSetting\UserSetting::updateDB();
?>
<#2>
<?php
\srag\Plugins\UserDefaults\UserSetting\UserSetting::updateDB();
?>
<#3>
<?php
\srag\Plugins\UserDefaults\UserSetting\UserSetting::updateDB();
?>
<#4>
<?php
\srag\Plugins\UserDefaults\UserSetting\UserSetting::updateDB();
?>
<#5>
<?php
//
?>
<#6>
<?php
\srag\Plugins\UserDefaults\UserSetting\UserSetting::updateDB();
?>
<#7>
<?php
\srag\Plugins\UserDefaults\UserSetting\UserSetting::updateDB();
?>
<#8>
<?php
/**
 * @var \srag\Plugins\UserDefaults\UserSetting\UserSetting $ilUserSetting
 */
foreach (\srag\Plugins\UserDefaults\UserSetting\UserSetting::get() as $ilUserSetting) {
    $ilUserSetting->setOnCreate(true);
    $ilUserSetting->setOnUpdate(false);
    $ilUserSetting->setOnManual(true);
    $ilUserSetting->update();
}
?>
<#9>
<?php
\srag\Plugins\UserDefaults\UserSetting\UserSetting::updateDB();
?>
<#10>
<?php
\srag\Plugins\UserDefaults\Config\UserDefaultsConfig::updateDB();
?>
<#11>
<?php
//
?>
<#12>
<?php
//
?>
<#13>
<?php
global $DIC;
if ($DIC->database()->tableExists(\srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld::TABLE_NAME)) {
    \srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld::updateDB();

    if ($DIC->database()->tableColumnExists(\srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld::TABLE_NAME, "udf_field_id")
        && $DIC->database()->tableColumnExists(
            \srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld::TABLE_NAME,
            "field_key"
        )) {
        $DIC->database()->dropTableColumn(\srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld::TABLE_NAME, "field_key");
    }

    $DIC->database
        ->renameTableColumn(\srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld::TABLE_NAME, "udf_field_id", "field_key");

    $DIC->d->modifyTableColumn(\srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld::TABLE_NAME, "field_key", [
        "type" => "text"
    ]);
}
?>
<#14>
<?php
global $DIC;
if ($DIC->database()->tableExists(\srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld::TABLE_NAME)) {
    \srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld::updateDB();

    /**
     * @var \srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld $UDFCheckOld
     */
    foreach (
        \srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld::where([
            "field_category" => 0
        ])->get() as $UDFCheckOld
    ) {
        $UDFCheckOld->setFieldCategory(\srag\Plugins\UserDefaults\UDFCheck\UDFCheckUDF::FIELD_CATEGORY);
        $UDFCheckOld->store();
    }
}
?>
<#15>
<?php
global $DIC;
if ($DIC->database()->tableExists(\srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld::TABLE_NAME)) {
    \srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld::updateDB();

    $DIC->database()->modifyTableColumn(\srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld::TABLE_NAME, "field_key", [
        "type" => "text",
        "length" => "256"
    ]);
}
?>
<#16>
<?php
global $DIC;
\srag\Plugins\UserDefaults\UDFCheck\UDFCheckUser::updateDB();
\srag\Plugins\UserDefaults\UDFCheck\UDFCheckUDF::updateDB();

if ($DIC->database()->tableExists(\srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld::TABLE_NAME)) {
    \srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld::updateDB();

    /**
     * @var \srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld $UDFCheckOld
     */
    foreach (\srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld::get() as $UDFCheckOld) {
        $UDFCheck = \srag\Plugins\UserDefaults\UDFCheck\UDFCheck::newInstance($UDFCheckOld->getFieldCategory());

        $UDFCheck->setParentId($UDFCheckOld->getParentId());
        $UDFCheck->setFieldKey($UDFCheckOld->getFieldKey());
        $UDFCheck->setCheckValue($UDFCheckOld->getCheckValue());
        $UDFCheck->setOperator($UDFCheckOld->getOperator());
        $UDFCheck->setNegated($UDFCheckOld->isNegated());
        $UDFCheck->setOwner($UDFCheckOld->getOwner());
        $UDFCheck->setStatus($UDFCheckOld->getStatus());
        $UDFCheck->setCreateDate($UDFCheckOld->getCreateDate());
        $UDFCheck->setUpdateDate($UDFCheckOld->getUpdateDate());

        $UDFCheck->store();
    }

    $DIC->database()->dropTable(\srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld::TABLE_NAME, false);
}
?>
<#17>
<?php
\srag\Plugins\UserDefaults\UserSetting\UserSetting::updateDB();
?>
<#18>
<?php
\srag\Plugins\UserDefaults\UserSetting\UserSetting::updateDB();
?>
<#19>
<?php
\srag\Plugins\UserDefaults\UserSetting\UserSetting::updateDB();
?>
<#20>
<?php
\srag\Plugins\UserDefaults\UserSetting\UserSetting::updateDB();
?>
<#21>
<?php
global $DIC;
\srag\Plugins\UserDefaults\UserSetting\UserSetting::updateDB();
$usr_setting_table = \srag\Plugins\UserDefaults\UserSetting\UserSetting::TABLE_NAME;
if ($DIC->database()->tableColumnExists($usr_setting_table, 'global_role')) {
    $DIC->database()->query(
        'UPDATE ' . $usr_setting_table . ' SET global_roles = CONCAT("[", global_role, "]") WHERE true'
    );
    $DIC->database()->dropTableColumn($usr_setting_table, 'global_role');
}
?>
<#22>
<?php
\srag\Plugins\UserDefaults\UserSetting\UserSetting::updateDB();
?>
<#23>
<?php
global $DIC;
if ($DIC->database()->tableColumnExists(
    \srag\Plugins\UserDefaults\UserSetting\UserSetting::TABLE_NAME,
    "assigned_courses_desktop"
)) {
    $DIC->database()->dropTableColumn(
        \srag\Plugins\UserDefaults\UserSetting\UserSetting::TABLE_NAME,
        "assigned_courses_desktop"
    );
}

if ($DIC->database()->tableColumnExists(
    \srag\Plugins\UserDefaults\UserSetting\UserSetting::TABLE_NAME,
    "assigned_categories_desktop"
)) {
    $DIC->database()->dropTableColumn(
        \srag\Plugins\UserDefaults\UserSetting\UserSetting::TABLE_NAME,
        "assigned_categories_desktop"
    );
}

if ($DIC->database()->tableColumnExists(
    \srag\Plugins\UserDefaults\UserSetting\UserSetting::TABLE_NAME,
    "assigned_groupes_desktop"
)) {
    $DIC->database()->dropTableColumn(
        \srag\Plugins\UserDefaults\UserSetting\UserSetting::TABLE_NAME,
        "assigned_groupes_desktop"
    );
}
?>
<#24>
<?php
global $DIC;
if (!$DIC->database()->tableColumnExists(
    \srag\Plugins\UserDefaults\UserSetting\UserSetting::TABLE_NAME,
    "unsubscr_from_grp"
)) {
    $DIC->database()->addTableColumn(
        \srag\Plugins\UserDefaults\UserSetting\UserSetting::TABLE_NAME,
        "unsubscr_from_grp",
        ["type" => "integer"]
    );
}
?>
<#25>
<?php
\srag\Plugins\UserDefaults\UserSetting\UserSetting::updateDB();
?>
<#26>
<?php
\srag\Plugins\UserDefaults\UserSetting\UserSetting::updateDB();
?>
<#27>
<?php
\srag\Plugins\UserDefaults\UserSetting\UserSetting::updateDB();
?>
<#28>
<?php
/**
 * ILIAS 11 migrated udf_definition.field_id from integer IDs to UUIDs and left
 * behind udf_field_id_map so plugins can pull their own references along. That
 * table is scheduled for removal with ILIAS 12 (components/ILIAS/User/ROADMAP.md),
 * so this step has to run while the installation is still on 11.
 *
 * Skipped silently when the map is absent: a fresh install on ILIAS 11 never had
 * integer keys to begin with.
 */
global $DIC;

$db = $DIC->database();
$udf_check_table = \srag\Plugins\UserDefaults\UDFCheck\UDFCheckUDF::TABLE_NAME;
$setting_table = \srag\Plugins\UserDefaults\UserSetting\UserSetting::TABLE_NAME;

if ($db->tableExists('udf_field_id_map') && $db->tableExists($udf_check_table)) {
    $map = [];
    $map_result = $db->query('SELECT old_field_id, field_id FROM udf_field_id_map');
    while ($map_row = $db->fetchAssoc($map_result)) {
        $map[(string) $map_row['old_field_id']] = (string) $map_row['field_id'];
    }

    $unmapped = [];
    $check_result = $db->query('SELECT id, parent_id, field_key FROM ' . $db->quoteIdentifier($udf_check_table));
    while ($check_row = $db->fetchAssoc($check_result)) {
        $field_key = (string) $check_row['field_key'];

        // Already a UUID, so a repeated update run is a no-op.
        if (!ctype_digit($field_key)) {
            continue;
        }

        if (!isset($map[$field_key])) {
            // The custom field was deleted in the core before the update.
            $unmapped[(int) $check_row['parent_id']][] = $field_key;
            continue;
        }

        $db->update(
            $udf_check_table,
            ['field_key' => [\ilDBConstants::T_TEXT, $map[$field_key]]],
            ['id' => [\ilDBConstants::T_INTEGER, (int) $check_row['id']]]
        );
    }

    if ($unmapped !== []) {
        // Such a check can no longer match (see UDFCheck::isValid()), which
        // silently changes who the setting applies to. Take the setting out of
        // service instead, so an administrator replaces the check on purpose.
        $db->manipulate(
            'UPDATE ' . $db->quoteIdentifier($setting_table)
            . ' SET status = ' . $db->quote(\srag\Plugins\UserDefaults\UserSetting\UserSetting::STATUS_INACTIVE, \ilDBConstants::T_INTEGER)
            . ' WHERE ' . $db->in('id', array_keys($unmapped), false, \ilDBConstants::T_INTEGER)
        );

        if ($DIC->isDependencyAvailable('logger')) {
            foreach ($unmapped as $setting_id => $field_keys) {
                $DIC->logger()->root()->warning(
                    'UserDefaults: deactivated setting ' . $setting_id . ', its checks on custom user field id(s) '
                    . implode(', ', array_unique($field_keys)) . ' have no UUID mapping in udf_field_id_map.'
                );
            }
        }
    }
}
?>
<#29>
<?php
/**
 * ILIAS 11 renamed usr_data.country (free text) to old_country and
 * usr_data.sel_country (country code) to country, then moves old_country into
 * a custom field or deletes it, depending on the administrator's choice in
 * UserProfileMigrations. ilObjUser::getSelectedCountry() is gone.
 *
 * So sel_country checks become country checks, and the former free text
 * country checks lose their field: they are renamed to old_country, which no
 * definition offers and therefore never matches, and their settings are
 * deactivated until an administrator rebuilds the check on the new field.
 */
global $DIC;

$db = $DIC->database();
$user_check_table = \srag\Plugins\UserDefaults\UDFCheck\UDFCheckUser::TABLE_NAME;
$setting_table = \srag\Plugins\UserDefaults\UserSetting\UserSetting::TABLE_NAME;

if ($db->tableExists($user_check_table)) {
    $quoted_table = $db->quoteIdentifier($user_check_table);

    $free_text = [];
    $result = $db->queryF(
        'SELECT parent_id FROM ' . $quoted_table . ' WHERE field_key = %s',
        [\ilDBConstants::T_TEXT],
        ['country']
    );
    while ($row = $db->fetchAssoc($result)) {
        $free_text[] = (int) $row['parent_id'];
    }
    $free_text = array_values(array_unique($free_text));

    // One statement, so the two renames can never run into each other.
    $db->manipulateF(
        'UPDATE ' . $quoted_table . ' SET field_key = CASE field_key WHEN %s THEN %s WHEN %s THEN %s END'
        . ' WHERE field_key IN (%s, %s)',
        array_fill(0, 6, \ilDBConstants::T_TEXT),
        ['country', 'old_country', 'sel_country', 'country', 'country', 'sel_country']
    );

    if ($free_text !== []) {
        $db->manipulate(
            'UPDATE ' . $db->quoteIdentifier($setting_table)
            . ' SET status = ' . $db->quote(\srag\Plugins\UserDefaults\UserSetting\UserSetting::STATUS_INACTIVE, \ilDBConstants::T_INTEGER)
            . ' WHERE ' . $db->in('id', $free_text, false, \ilDBConstants::T_INTEGER)
        );

        if ($DIC->isDependencyAvailable('logger')) {
            $DIC->logger()->root()->warning(
                'UserDefaults: deactivated setting(s) ' . implode(', ', $free_text)
                . ', their checks on the free text country field no longer have a source in ILIAS 11.'
            );
        }
    }
}
?>
