<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Mapper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Db\ResultSet\HydratingResultSet;
use Application\Entity\HistoryLogin;
use Interop\Container\ContainerInterface;

/**
 * Description of HistoryLoginMapperFactory
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class HistoryLoginMapperFactory implements FactoryInterface
{

    /**
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return \Helper\Mapper\HistoryLoginMapper
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $historyLoginMapper = new HistoryLoginMapper('history_login',
                $serviceLocator->get('dbAdapter'));
        return $historyLoginMapper;
    }

    /**
     *
     * @param ContainerInterface $container
     * @param type $requestedName
     * @param array $options
     * @return \Helper\Mapper\HistoryLoginMapper
     */
    public function __invoke(ContainerInterface $container, $requestedName,
            array $options = null)
    {

        $historyLoginMapper = new HistoryLoginMapper('history_login',
                $container->get('dbAdapter'));
        return $historyLoginMapper;
    }

}
