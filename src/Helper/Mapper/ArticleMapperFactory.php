<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Mapper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Helper\Mapper\ArticleMapper;
use Interop\Container\ContainerInterface;

/**
 * Description of ArticleMapperFactory
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class ArticleMapperFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $pageMapper = new ArticleMapper('article',
                $serviceLocator->get('dbAdapter'), null);
        return $pageMapper;
    }

    public function __invoke(ContainerInterface $container, $requestedName,
            array $options = null)
    {
        $pageMapper = new ArticleMapper('article', $container->get('dbAdapter'));
        return $pageMapper;
    }

}
