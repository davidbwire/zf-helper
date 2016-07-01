<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Controller\Plugin;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Helper\Controller\Plugin\Logger;

/**
 * Description of LoggerFactory
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class LoggerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $pluginManager)
    {
        $serviceLocator = $pluginManager->getServiceLocator();
        $loggerPlugin = new Logger($serviceLocator->get('LoggerService'));
        return $loggerPlugin;
    }

}
