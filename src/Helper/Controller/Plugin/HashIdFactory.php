<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Controller\Plugin;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Helper\Controller\Plugin\HashId;
use Interop\Container\ContainerInterface;

/**
 * Description of HashIdFactory
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class HashIdFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $hashId = new HashId($serviceLocator);
        return $hashId;
    }

    public function __invoke(ContainerInterface $container, $requestedName,
            array $options = null)
    {
        $hashId = new HashId($container);
        return $hashId;
    }

}
