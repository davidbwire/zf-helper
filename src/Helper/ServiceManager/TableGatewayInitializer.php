<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\ServiceManager;

use Interop\Container\ContainerInterface;
use Helper\Mapper\TableGateway;
use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Description of TableGatewayInitializer
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class TableGatewayInitializer implements InitializerInterface
{

    /**
     * zf3
     * 
     * @param ContainerInterface $container
     * @param TableGateway $instance
     * @return type
     */
    public function __invoke(ContainerInterface $container, $instance)
    {
        if (!$instance instanceof TableGateway) {
            return;
        }
        $instance->setLogger($container->get('logger'));
    }

    /**
     * zf2
     * 
     * @param TableGateway $instance
     * @param ServiceLocatorInterface $serviceLocator
     * @return void
     */
    public function initialize($instance,
            ServiceLocatorInterface $serviceLocator)
    {
        if (!$instance instanceof TableGateway) {
            return;
        }
        $instance->setLogger($serviceLocator->get('logger'));
    }

}
