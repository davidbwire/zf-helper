<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Mapper\Junction;

use Helper\Mapper\Junction\UserHasRoleMapper;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Db\ResultSet\HydratingResultSet;
use Application\Entity\Junction\UserHasRole;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Description of UserHasRoleMapperFactory
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class UserHasRoleMapperFactory implements FactoryInterface
{

    
    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $resultSetPrototype = new HydratingResultSet(new ClassMethods(true),
                new UserHasRole());
        $userHasRoleMapper = new UserHasRoleMapper('user_has_role',
                $serviceLocator->get('DbAdapter'), null, $resultSetPrototype);
        return $userHasRoleMapper;
    }

}
