<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Description of HashIdFactory
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class HashIdFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $hashId = new HashId($serviceLocator);
        return $hashId;
    }

}
