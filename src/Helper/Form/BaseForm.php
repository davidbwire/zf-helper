<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Form;

use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Form\Element\Csrf as CsrfElement;

/**
 * Description of BaseForm
 *
 * Boilerplate for Forms that use method post and classmethods hydrator underscore true
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class BaseForm extends Form
{

    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
        $this->setAttribute('method', 'post');
        $this->setHydrator(new ClassMethods(true));
        // add a security key for all input forms
        $csrfElement = new CsrfElement('wwjd');
        // default to 10 minutes ttl
        $csrfElement->setOption('timeout', 600);
        $this->add($csrfElement);
    }

}
