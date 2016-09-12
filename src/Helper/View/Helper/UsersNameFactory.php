<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

/**
 * Description of UsersNameFactory
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class UsersNameFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $usernameHelper = new UsersName($serviceLocator->getServiceLocator()
                        ->get('UserMapper'));
        return $usernameHelper;
    }

    public function __invoke(ContainerInterface $container, $requestedName,
            array $options = null)
    {
        $usernameHelper = new UsersName($container->getServiceLocator()
                        ->get('UserMapper'));
        return $usernameHelper;
    }

}
