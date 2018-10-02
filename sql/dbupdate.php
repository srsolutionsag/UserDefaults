<#1>
<?php
\srag\Plugins\UserDefaults\UDFCheck\UDFCheck::updateDB();
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
\srag\Plugins\UserDefaults\UDFCheck\UDFCheck::updateDB();
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
\srag\Plugins\UserDefaults\Config\Config::updateDB();
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
\srag\DIC\DICStatic::dic()->database()->renameTableColumn(\srag\Plugins\UserDefaults\UDFCheck\UDFCheck::TABLE_NAME, "udf_field_id", "field_key");
\srag\DIC\DICStatic::dic()->database()->modifyTableColumn(\srag\Plugins\UserDefaults\UDFCheck\UDFCheck::TABLE_NAME, "field_key", [
	"type" => "text"
]);
?>
<#14>
<?php
\srag\Plugins\UserDefaults\UDFCheck\UDFCheck::updateDB();
/**
 * @var \srag\Plugins\UserDefaults\UDFCheck\UDFCheck $UDFCheck
 */
foreach (\srag\Plugins\UserDefaults\UDFCheck\UDFCheck::get() as $UDFCheck) {
	$UDFCheck->setFieldCategory($UDFCheck::FIELD_CATEGORY_UDF);
	$UDFCheck->update();
}
?>