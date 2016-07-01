<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Controller\Plugin;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * Description of AssignRoleFactory
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class AssignRoleFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $pluginManager)
    {
        $assignRolePlugin = new AssignRole($pluginManager->getServiceLocator()->get('DbAdapter'));
        return $assignRolePlugin;
    }

}
