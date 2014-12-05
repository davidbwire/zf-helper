<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Mapper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Application\Entity\HistoryFailedLogin;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Db\ResultSet\HydratingResultSet;

/**
 * Description of HistoryFailedLoginMapperFactory
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class HistoryFailedLoginMapperFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $resultSetPrototype = new HydratingResultSet(new ClassMethods(true),
                new HistoryFailedLogin());
        $historyFailedLoginMapper = new HistoryFailedLoginMapper('history_failed_login',
                $serviceLocator->get('DbAdapter'), null, $resultSetPrototype);
        // Set a ServiceLocator lazy loading LoggerService etc.
        $historyFailedLoginMapper->setServiceLocator($serviceLocator);
        return $historyFailedLoginMapper;
    }

}
