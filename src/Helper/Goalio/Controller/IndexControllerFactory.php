<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Goalio\Controller;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Helper\Goalio\Controller\IndexController;
use Interop\Container\ContainerInterface;

/**
 * Description of IndexControllerFactory
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class IndexControllerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $indexController = new IndexController($serviceLocator);
        return $indexController;
    }

    public function __invoke(ContainerInterface $container, $requestedName,
            array $options = null)
    {
        $indexController = new IndexController($container);
        return $indexController;
    }

}
