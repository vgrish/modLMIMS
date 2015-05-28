<?php
/**
 * Loads system settings into build
 *
 * @package lmims
 * @subpackage build
 */
$settings = array();

$tmp = array(
	'lmims_refresh_lmims_data_about_page_on_save' => array(
		'value' => '1'
		,'xtype' => 'combo-boolean'
		,'area' => 'lmims_area'
	)
);


foreach ($tmp as $k => $v) {
	/* @var modSystemSetting $setting */
	$setting = $modx->newObject('modSystemSetting');
	$setting->fromArray(array_merge(
		array(
			'key' => $k
			,'namespace' => 'lmims'
		), $v
	),'',true,true);

	$settings[] = $setting;
}

return $settings;