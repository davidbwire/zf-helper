<?php

namespace Helper\Form;

use Zend\Form\Form;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Helper\Form\PageFilter;
use Zend\Stdlib\Hydrator\ClassMethods;

/**
 * Description of PageEditForm
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class PageEditForm extends Form implements ServiceLocatorAwareInterface
{

    /**
     *
     * @var type
     */
    protected $serviceLocator;

    public function __construct($name = 'page-edit', $options = array())
    {
        parent::__construct($name, $options);
        $this->setAttribute('method', 'post');
        $this->setHydrator(new ClassMethods(true));
        // just validate the content body
        $this->setValidationGroup(array('content'));

        $this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'options' => array(
                'label' => 'edit page',
                'class' => 'btn btn-xl btn-success',
            )
        ));
    }

    public function init()
    {
        $pageFieldset = $this->serviceLocator->get('Helper\Form\Fieldset\PageFieldset');
        $pageFieldset->setUseAsBaseFieldset(true);
        $this->add($pageFieldset);
        $this->setInputFilter(new PageFilter());
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

}
