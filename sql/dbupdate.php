<#1>
<?php
require_once "Customizing/global/plugins/Services/EventHandling/EventHook/UserDefaults/vendor/autoload.php";
ilUDFCheck::updateDB();
ilUserSetting::updateDB();
?>
<#2>
<?php
require_once "Customizing/global/plugins/Services/EventHandling/EventHook/UserDefaults/vendor/autoload.php";
ilUserSetting::updateDB();
?>
<#3>
<?php
require_once "Customizing/global/plugins/Services/EventHandling/EventHook/UserDefaults/vendor/autoload.php";
ilUserSetting::updateDB();
?>
<#4>
<?php
require_once "Customizing/global/plugins/Services/EventHandling/EventHook/UserDefaults/vendor/autoload.php";
ilUserSetting::updateDB();
?>
<#5>
<?php
require_once "Customizing/global/plugins/Services/EventHandling/EventHook/UserDefaults/vendor/autoload.php";
ilUDFCheck::updateDB();
?>
<#6>
<?php
require_once "Customizing/global/plugins/Services/EventHandling/EventHook/UserDefaults/vendor/autoload.php";
ilUserSetting::updateDB();
?>
<#7>
<?php
require_once "Customizing/global/plugins/Services/EventHandling/EventHook/UserDefaults/vendor/autoload.php";
ilUserSetting::updateDB();
?>
<#8>
<?php
require_once "Customizing/global/plugins/Services/EventHandling/EventHook/UserDefaults/vendor/autoload.php";
/**
 * @var $ilUserSetting ilUserSetting
 */
foreach (ilUserSetting::get() as $ilUserSetting) {
	$ilUserSetting->setOnCreate(true);
	$ilUserSetting->setOnUpdate(false);
	$ilUserSetting->setOnManual(true);
	$ilUserSetting->update();
}
?>
<#9>
<?php
require_once "Customizing/global/plugins/Services/EventHandling/EventHook/UserDefaults/vendor/autoload.php";
ilUserSetting::updateDB();
?>