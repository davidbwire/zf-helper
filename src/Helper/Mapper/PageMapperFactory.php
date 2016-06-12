<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Mapper;

use Zend\ServiceManager\FactoryInterface;
use Application\Entity\Page;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Helper\Mapper\PageMapper;

/**
 * Description of PageMapperFactory
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class PageMapperFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $resultSetPrototype = new HydratingResultSet(new ClassMethods(true),
                new Page());
        $pageMapper = new PageMapper('article',
                $serviceLocator->get('DbAdapter'), null, $resultSetPrototype);
        return $pageMapper;
    }

}
