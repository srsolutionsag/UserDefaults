<#1>
<?php

use srag\Plugins\UserDefaults\UserSetting\UserSetting;

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

	if($DIC->database()->tableColumnExists(\srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld::TABLE_NAME,"udf_field_id")
		&& $DIC->database()->tableColumnExists(\srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld::TABLE_NAME,"field_key")) {
		$DIC->database()->dropTableColumn(\srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld::TABLE_NAME,"field_key");
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
	foreach (\srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld::where([
		"field_category" => 0
	])->get() as $UDFCheckOld) {
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
    $DIC->database()->query('UPDATE ' . $usr_setting_table . ' SET global_roles = CONCAT("[", global_role, "]") WHERE true');
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
if ($DIC->database()->tableColumnExists(\srag\Plugins\UserDefaults\UserSetting\UserSetting::TABLE_NAME, "assigned_courses_desktop"))
    $DIC->database()->dropTableColumn(\srag\Plugins\UserDefaults\UserSetting\UserSetting::TABLE_NAME, "assigned_courses_desktop");

if ($DIC->database()->tableColumnExists(\srag\Plugins\UserDefaults\UserSetting\UserSetting::TABLE_NAME, "assigned_categories_desktop"))
    $DIC->database()->dropTableColumn(\srag\Plugins\UserDefaults\UserSetting\UserSetting::TABLE_NAME, "assigned_categories_desktop");

if ($DIC->database()->tableColumnExists(\srag\Plugins\UserDefaults\UserSetting\UserSetting::TABLE_NAME, "assigned_groupes_desktop"))
    $DIC->database()->dropTableColumn(\srag\Plugins\UserDefaults\UserSetting\UserSetting::TABLE_NAME, "assigned_groupes_desktop");
?>
<#24>
<?php
global $DIC;
if (!$DIC->database()->tableColumnExists(\srag\Plugins\UserDefaults\UserSetting\UserSetting::TABLE_NAME, "unsubscr_from_grp"))
    $DIC->database()->addTableColumn(\srag\Plugins\UserDefaults\UserSetting\UserSetting::TABLE_NAME, "unsubscr_from_grp", ["type" => "integer"]);
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