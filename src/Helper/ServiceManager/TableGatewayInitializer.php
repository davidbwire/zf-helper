<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\ServiceManager;

use Zend\ServiceManager\Initializer\InitializerInterface;
use Interop\Container\ContainerInterface;
use Helper\Mapper\TableGateway;

/**
 * Description of TableGatewayInitializer
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class TableGatewayInitializer implements InitializerInterface
{

    public function __invoke(ContainerInterface $container, $instance)
    {
        if (!$instance instanceof TableGateway) {
            return;
        }
        $instance->setLogger($container->get('logger'));
    }

}
