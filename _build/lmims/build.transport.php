<?php
/**
 * lmims build script
 *
 * @package lmims 
 * @subpackage build
 */

error_reporting(E_ALL);
ini_set("display_errors", 1);
ini_set('max_execution_time', 0);
set_time_limit(0);

echo '<pre>';

$mtime	= microtime();
$mtime	= explode(' ', $mtime);
$mtime	= $mtime[1] + $mtime[0];
$tstart	= $mtime;

require_once 'build.config.php';

/* define sources */
$root		= dirname(dirname(dirname(__FILE__))).'/';
$sources	= array(
	'root'			=> $root,
	'build'			=> $root.'_build/'. PKG_NAME_LOWER .'/',
	'data'			=> $root.'_build/'. PKG_NAME_LOWER .'/data/',
	'resolvers'		=> $root.'_build/'. PKG_NAME_LOWER .'/resolvers/',
	'source_core'	=> $root.'core/components/'.PKG_NAME_LOWER .'/',
	'docs'			=> $root.'core/components/'.PKG_NAME_LOWER.'/docs/',
	'lexicon'		=> $root.'core/components/'.PKG_NAME_LOWER.'/lexicon/',
	'plugins'		=> $root.'core/components/'.PKG_NAME_LOWER.'/elements/plugins/',
	'source_assets'	=> $root.'assets/components/'.PKG_NAME_LOWER .'/'
);
unset($root);

require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
require_once $sources['build'] . '/includes/functions.php';

$modx = new modX();
$modx->initialize('mgr');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');

$modx->loadClass('transport.modPackageBuilder','',false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME_LOWER,PKG_VERSION,PKG_RELEASE);
$builder->registerNamespace(PKG_NAME_LOWER,false,true,'{core_path}components/'.PKG_NAME_LOWER.'/');
$modx->log(modX::LOG_LEVEL_INFO,'Created Transport Package and Namespace.');


/* create category */
$modx->log(xPDO::LOG_LEVEL_INFO,'Created category.');
/* @var modCategory $category */
$category = $modx->newObject('modCategory');
$category->set('id',1);
$category->set('category',PKG_NAME);

/* load system settings */
$settings = include $sources['data'].'transport.settings.php';
if (!is_array($settings)) {
	$modx->log(modX::LOG_LEVEL_ERROR,'Could not package in settings.');
} else {
	$attributes= array(
		xPDOTransport::UNIQUE_KEY => 'key',
		xPDOTransport::PRESERVE_KEYS => true,
		xPDOTransport::UPDATE_OBJECT => BUILD_SETTING_UPDATE,
	);
	foreach ($settings as $setting) {
		$vehicle = $builder->createVehicle($setting,$attributes);
		$builder->putVehicle($vehicle);
	}
	$modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($settings).' System Settings.');
}
unset($settings,$setting,$attributes);

/* add plugins */
$plugins = include $sources['data'].'transport.plugins.php';
if (!is_array($plugins)) {
	$modx->log(modX::LOG_LEVEL_ERROR,'Could not package in plugins.');
} else {
	$category->addMany($plugins);
	$modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($plugins).' plugins.');
}

/* create category vehicle */
$attr = array(
	xPDOTransport::UNIQUE_KEY => 'category',
	xPDOTransport::PRESERVE_KEYS => false,
	xPDOTransport::UPDATE_OBJECT => true,
	xPDOTransport::RELATED_OBJECTS => true,
	xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array(
		'Plugins' => array(
			xPDOTransport::PRESERVE_KEYS => false,
			xPDOTransport::UPDATE_OBJECT => BUILD_PLUGIN_UPDATE,
			xPDOTransport::UNIQUE_KEY => 'name',
		),
		'PluginEvents' => array(
			xPDOTransport::PRESERVE_KEYS => true,
			xPDOTransport::UPDATE_OBJECT => BUILD_EVENT_UPDATE,
			xPDOTransport::UNIQUE_KEY => array('pluginid','event'),
		),
	),
);
$vehicle = $builder->createVehicle($category,$attr);

/* now pack in resolvers */
$vehicle->resolve('file', array(
	'source' => $sources['source_assets'],
	'target' => "return MODX_ASSETS_PATH . 'components/';",
));
$vehicle->resolve('file', array(
	'source' => $sources['source_core'],
	'target' => "return MODX_CORE_PATH . 'components/';",
));

$resolvers = array('extension','tables');
foreach ($resolvers as $resolver) {
	if ($vehicle->resolve('php', array(
		'source' => $sources['resolvers'] . 'resolve.'.$resolver.'.php'
	))) {
		$modx->log(modX::LOG_LEVEL_INFO,'Added resolver "'.$resolver.'" to category.');
	} else {
		$modx->log(modX::LOG_LEVEL_INFO,'Could not add resolver "'.$resolver.'" to category.');
	}
}

flush();
$builder->putVehicle($vehicle);

/* now pack in the license file, readme and setup options */
$builder->setPackageAttributes(array(
	'changelog'	=> file_get_contents($sources['docs'] . 'changelog.txt')
	,'license'	=> file_get_contents($sources['docs'] . 'license.txt')
	,'readme'	=> file_get_contents($sources['docs'] . 'readme.txt')
));
$modx->log(modX::LOG_LEVEL_INFO,'Added package attributes and setup options.');

/* zip up package */
$modx->log(modX::LOG_LEVEL_INFO,'Packing up transport package zip...');
$builder->pack();

$mtime	= microtime();
$mtime	= explode(" ", $mtime);
$mtime	= $mtime[1] + $mtime[0];
$tend	= $mtime;
$totalTime	= ($tend - $tstart);
$totalTime	= sprintf("%2.4f s", $totalTime);

$modx->log(modX::LOG_LEVEL_INFO,"\nPackage Built.\nExecution time: {$totalTime}\n");

exit();