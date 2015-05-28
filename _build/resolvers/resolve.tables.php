<?php

if ($object->xpdo) {
	/** @var modX $modx */
	$modx =& $object->xpdo;

	switch ($options[xPDOTransport::PACKAGE_ACTION]) {
		case xPDOTransport::ACTION_INSTALL:
			$modelPath = $modx->getOption('modlmims.core_path', null, $modx->getOption('core_path') . 'components/modlmims/') . 'model/';
			$modx->addPackage('modlmims', $modelPath);
			$manager = $modx->getManager();

			$objects = array(
				'modLMIMS',
			);
      $dontRemoveObjects = array(
//        'modLMIMSSomeClass',
      );
      $removeObjects = array(
//        'modLMIMSAnotherSomeClass',
      );

      foreach ($objects as $tmp) {
        if (!in_array($tmp, $dontRemoveObjects) || in_array($tmp, $removeObjects)) {
          $manager->removeObjectContainer($tmp);
        }
        $manager->createObjectContainer($tmp);
			}

      $modx->removeExtensionPackage('modlmims');
      $modx->addExtensionPackage('modlmims', $modx->getOption('modlmims.core_path', null, '[[++core_path]]components/modlmims/') .'model/');

      $level = $modx->getLogLevel();
      $modx->setLogLevel(xPDO::LOG_LEVEL_FATAL);
      $modx->setLogLevel($level);
      break;

		case xPDOTransport::ACTION_UPGRADE:
			break;

		case xPDOTransport::ACTION_UNINSTALL:
      $modx->removeExtensionPackage('modlmims');
			break;
	}
}
return true;
