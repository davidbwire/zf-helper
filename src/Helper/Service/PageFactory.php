<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Service;

use Zend\ServiceManager\FactoryInterface;

/**
 * Description of PageFactory
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class PageFactory implements FactoryInterface
{

    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $page = new Page();
        $page->setPageMapper($serviceLocator->get('\Helper\Mapper\PageMapper'))
                ->setNavigation($serviceLocator->get('Navigation'))
                ->setRequest($serviceLocator->get('Request'));
        return $page;
    }

}
