<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Mapper\Junction;

use Helper\Mapper\Junction\UserHasRoleMapper;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

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
                $serviceLocator->get('dbAdapter'), null, null);
        // inject role mapper
        $userHasRoleMapper->setRoleMapper(
                $serviceLocator->get('HelperRoleMapper'));
        return $userHasRoleMapper;
    }

    public function __invoke(ContainerInterface $container, $requestedName,
            array $options = null)
    {
        $userHasRoleMapper = new UserHasRoleMapper('user_has_role',
                $container->get('dbAdapter'), null, null);
        // inject role mapper
        $userHasRoleMapper->setRoleMapper(
                $container->get('HelperRoleMapper'));
        return $userHasRoleMapper;
    }

}
