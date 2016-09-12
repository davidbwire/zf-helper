<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Service;

use Zend\ServiceManager\FactoryInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Description of PageFactory
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class ArticleFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $page = new ArticleService();
        $page->setPageMapper($serviceLocator->get('\Helper\Mapper\PageMapper'))
                ->setNavigation($serviceLocator->get('Navigation'))
                ->setRequest($serviceLocator->get('Request'));
        return $page;
    }

    public function __invoke(ContainerInterface $container, $requestedName,
            array $options = null)
    {
        $page = new ArticleService();
        $page->setPageMapper($container->get('\Helper\Mapper\PageMapper'))
                ->setNavigation($container->get('Navigation'))
                ->setRequest($container->get('Request'));
        return $page;
    }

}
