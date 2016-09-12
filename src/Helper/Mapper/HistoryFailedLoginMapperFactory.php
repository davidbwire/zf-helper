<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Mapper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

/**
 * Description of HistoryFailedLoginMapperFactory
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class HistoryFailedLoginMapperFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $historyFailedLoginMapper = new HistoryFailedLoginMapper('history_failed_login',
                $serviceLocator->get('dbAdapter'));
        // Set a ServiceLocator lazy loading LoggerService etc.
        $historyFailedLoginMapper->setServiceLocator($serviceLocator);
        return $historyFailedLoginMapper;
    }

    public function __invoke(ContainerInterface $container, $requestedName,
            array $options = null)
    {
        $historyFailedLoginMapper = new HistoryFailedLoginMapper('history_failed_login',
                $container->get('dbAdapter'));
        // Set a ServiceLocator lazy loading LoggerService etc.
        $historyFailedLoginMapper->setServiceLocator($container);
        return $historyFailedLoginMapper;
    }

}
