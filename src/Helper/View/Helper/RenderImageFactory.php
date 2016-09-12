<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\HelperPluginManager;
use Interop\Container\ContainerInterface;

/**
 * Description of RenderImageFactory
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class RenderImageFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sm = $serviceLocator->getServiceLocator();
        $renderImageHelper = new RenderImage($sm->get('DbAdapter'),
                $sm->get('Logger'));
        return $renderImageHelper;
    }

    public function __invoke(ContainerInterface $container, $requestedName,
            array $options = null)
    {
        $renderImageHelper = new RenderImage($container->get('dbAdapter'),
                $container->get('logger'));
        return $renderImageHelper;
    }

}
