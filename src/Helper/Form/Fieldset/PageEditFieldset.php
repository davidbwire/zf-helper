<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */
use Helper\Form\Fieldset\PageFieldset;

namespace Helper\Form\Fieldset;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\ResultSet\ResultSetInterface;

/**
 * Description of PageEditFieldset
 *
 * Adds the dropdown
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class PageEditFieldset extends PageFieldset implements ServiceLocatorAwareInterface
{

    /**
     *
     * @var Zend\ServiceManager\ServiceLocatorAwareInterface
     */
    protected $serviceLocator;

    public function __construct($name = 'page', $options = array())
    {
        parent::__construct($name, $options);
    }

    public function init()
    {
        // composed of category(about-us) and title(About Us)
        $this->add(array(
            'name' => 'link',
            'type' => 'select',
            'attributes' => array(
                'class' => 'site-menu form-control',
            ),
            'options' => array(
                'value_options' => $this->fetchDropdown(),
            ),
        ));
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     *
     * @return array
     */
    private function fetchDropdown()
    {

        $pageMapper = $this->serviceLocator
                ->getServiceLocator()
                ->get('\Helper\Mapper\PageMapper');
        $resultset = $pageMapper->fetchAll(array('fields' => array('title', 'url_string')));
        if ($resultset instanceof ResultSetInterface) {
            $aData = array();
            foreach ($resultset as $page) {
                $aData[$page->getUrlString()] = $page->getTitle();
            }
            return $aData;
        } else {
            // no data was found
            return array();
        }
    }

}
