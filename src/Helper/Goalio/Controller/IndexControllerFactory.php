<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Goalio\Controller;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Helper\Goalio\Controller\IndexController;

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

}
