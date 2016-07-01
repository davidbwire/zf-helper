<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Helper\Service\SimpleMailer;

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

}
