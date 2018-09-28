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
foreach (\srag\Plugins\UserDefaults\UDFCheck\UDFCheck::get() as $udf_check) {// TODO: @mstuder
	/**
	 * @var \srag\Plugins\UserDefaults\UDFCheck\UDFCheck $udf_check
	 */
	$udf_check->setUdfFieldId("f_" . $udf_check->getUdfFieldId());
	$udf_check->store();
}

\srag\DIC\DICStatic::dic()->database()->modifyTableColumn(\srag\Plugins\UserDefaults\UDFCheck\UDFCheck::TABLE_NAME, "udf_field_id", [
	"type" => "text"
]);
?>
