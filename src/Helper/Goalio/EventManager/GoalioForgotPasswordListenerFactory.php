<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Goalio\EventManager;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Description of GoalioForgotPasswordListenerFactory
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class GoalioForgotPasswordListenerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $goalioForgotPasswordListener = new GoalioForgotPasswordListener($serviceLocator->get('DbAdapter'));
        // add the service locator to retreive mappers, etc
        $goalioForgotPasswordListener->setServiceLocator($serviceLocator);
        return $goalioForgotPasswordListener;
    }

}
