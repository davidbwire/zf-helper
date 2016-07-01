<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Mapper;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Db\ResultSet\HydratingResultSet;
use Application\Entity\Role;
use Helper\Mapper\RoleMapper;

/**
 * Description of RoleMapperFactory
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class RoleMapperFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $resultSetPrototype = new HydratingResultSet(new ClassMethods(true),
                new Role());
        $roleMapper = new RoleMapper('role', $serviceLocator->get('DbAdapter'),
                null, $resultSetPrototype);
        return $roleMapper;
    }

}
