<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Helper\Service\SimpleMailer;
use Interop\Container\ContainerInterface;

/**
 * Description of SimpleMailerFactory
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class SimpleMailerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $simpleMailer = new SimpleMailer($serviceLocator->get('Config'),
                // use view manager fro $sm as it has view resolvers
                $serviceLocator->get('ViewManager'),
                $serviceLocator->get('Logger'));
        return $simpleMailer;
    }

    public function __invoke(ContainerInterface $container, $requestedName,
            array $options = null)
    {
        $simpleMailer = new SimpleMailer($container->get('config'),
                // use view manager fro $sm as it has view resolvers
                $container->get('ViewManager'), $container->get('Logger'));
        return $simpleMailer;
    }

}
