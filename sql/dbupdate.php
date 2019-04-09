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
if (\srag\DIC\UserDefaults\DICStatic::dic()->database()->tableExists(\srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld::TABLE_NAME)) {
	\srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld::updateDB();

	if(\srag\DIC\UserDefaults\DICStatic::dic()->database()->tableColumnExists(\srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld::TABLE_NAME,"udf_field_id")
		&& \srag\DIC\UserDefaults\DICStatic::dic()->database()->tableColumnExists(\srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld::TABLE_NAME,"field_key")) {
		\srag\DIC\UserDefaults\DICStatic::dic()->database()->dropTableColumn(\srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld::TABLE_NAME,"field_key");
	}

	\srag\DIC\UserDefaults\DICStatic::dic()->database()
		->renameTableColumn(\srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld::TABLE_NAME, "udf_field_id", "field_key");

	\srag\DIC\UserDefaults\DICStatic::dic()->database()->modifyTableColumn(\srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld::TABLE_NAME, "field_key", [
		"type" => "text"
	]);
}
?>
<#14>
<?php
if (\srag\DIC\UserDefaults\DICStatic::dic()->database()->tableExists(\srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld::TABLE_NAME)) {
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
if (\srag\DIC\UserDefaults\DICStatic::dic()->database()->tableExists(\srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld::TABLE_NAME)) {
	\srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld::updateDB();

	\srag\DIC\UserDefaults\DICStatic::dic()->database()->modifyTableColumn(\srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld::TABLE_NAME, "field_key", [
		"type" => "text",
		"length" => "256"
	]);
}
?>
<#16>
<?php
\srag\Plugins\UserDefaults\UDFCheck\UDFCheckUser::updateDB();
\srag\Plugins\UserDefaults\UDFCheck\UDFCheckUDF::updateDB();

if (\srag\DIC\UserDefaults\DICStatic::dic()->database()->tableExists(\srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld::TABLE_NAME)) {
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

	\srag\DIC\UserDefaults\DICStatic::dic()->database()->dropTable(\srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld::TABLE_NAME, false);
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