<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Mapper\Junction;

use Helper\Mapper\Junction\UserHasRoleMapper;
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
        $userHasRoleMapper = new UserHasRoleMapper('user_has_role',
                $serviceLocator->get('DbAdapter'), null, null);
        return $userHasRoleMapper;
    }

}
