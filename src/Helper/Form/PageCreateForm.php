<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Form;

use Zend\Form\Form;
use Helper\Form\PageFilter;

/**
 * Description of PageCreateForm
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class PageCreateForm extends Form
{
    public function __construct($name = 'page-create', $options = array())
    {
        parent::__construct($name, $options);
        $this->setAttribute('method', 'post');
        $this->setHydrator(new ClassMethods(true));
        $this->setInputFilter(new PageFilter());
        // ensure the three items are provided
        // add more later
        $this->setValidationGroup(array('title', 'url_string', 'content'));


        $this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'options' => array(
                'label' => 'create page',
                'class' => 'btn btn-xl btn-success',
            )
        ));
    }

}
