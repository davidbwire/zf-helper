<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\ZfcUser\EventManager;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

/**
 * Description of ZfcUserListenerFactory
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class ZfcUserListenerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $zfcUserListener = new ZfcUserListener($serviceLocator);
        // set service locator for retreiving mappers etc
        $zfcUserListener->setServiceLocator($serviceLocator);
        return $zfcUserListener;
    }

    public function __invoke(ContainerInterface $container, $requestedName,
            array $options = null)
    {
        $zfcUserListener = new ZfcUserListener($container);
        // set service locator for retreiving mappers etc
        $zfcUserListener->setServiceLocator($container);
        return $zfcUserListener;
    }

}
