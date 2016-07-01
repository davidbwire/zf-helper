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

/**
 * Description of HistoryLoginMapperFactory
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class HistoryLoginMapperFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $resultSetPrototype = new HydratingResultSet(new ClassMethods(true),
                new HistoryLogin());
        $historyLoginMapper = new HistoryLoginMapper('history_login',
                $serviceLocator->get('DbAdapter'), null, $resultSetPrototype);
        return $historyLoginMapper;
    }

}
