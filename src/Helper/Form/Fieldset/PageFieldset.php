<?php

namespace Helper\Form\Fieldset;

use Zend\Form\Fieldset;
use Common\Entity\Page;

/**
 * Description of PageFieldset
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class PageFieldset extends Fieldset
{

    public function __construct($name = 'page', $options = array())
    {
        parent::__construct($name, $options);
        $this->setObject(new Page());
        // add the page title
        $this->add(array(
            'name' => 'title',
            'type' => 'text',
            'attributes' => array(
                'class' => 'form-control',
                'data-parsley-minlenght' => '6',
                'maxlenght' => 80,
            ),
        ));
        // add the url
        $this->add(array(
            'name' => 'url_string',
            'type' => 'text',
            'attributes' => array(
                'class' => 'form-control',
                'data-parsley-minlenght' => '6',
                'maxlenght' => 80,
            ),
        ));
        // this is the page content
        $this->add(array(
            'name' => 'content',
            'type' => 'textarea',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'content-body',
                'rows' => 20,
            ),
        ));
        $this->add(array(
            'type' => '\Zend\Form\Element\Text',
            'name' => 'label',
            'attributes' => array(
                'class' => 'form-control',
            ),
        ));
        $this->add(array(
            'type' => '\Zend\Form\Element\Text',
            'name' => 'route',
            'attributes' => array(
                'class' => 'form-control',
            ),
        ));
        $this->add(array(
            'type' => '\Zend\Form\Element\Text',
            'name' => 'is_published',
            'attributes' => array(
                'class' => 'form-control',
            ),
        ));
        $this->add(array(
            'type' => '\Zend\Form\Element\Text',
            'name' => 'priority',
            'attributes' => array(
                'class' => 'form-control',
            ),
        ));
        $this->add(array(
            'type' => '\Zend\Form\Element\Text',
            'name' => 'meta_title',
            'attributes' => array(
                'class' => 'form-control',
            ),
        ));
        $this->add(array(
            'type' => '\Zend\Form\Element\Text',
            'name' => 'meta_description',
            'attributes' => array(
                'class' => 'form-control',
            ),
        ));
        $this->add(array(
            'type' => '\Zend\Form\Element\Text',
            'name' => 'meta_keywords',
            'attributes' => array(
                'class' => 'form-control',
            ),
        ));
    }

}
