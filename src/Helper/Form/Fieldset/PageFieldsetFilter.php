<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Form\Fieldset;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;

/**
 * Description of PageFilter
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class PageFieldsetFilter extends InputFilter
{

    public function __construct($name = null)
    {
        parent::__construct($name);
        $factory = new InputFactory();
        $this->add($factory->createInput(array(
                    'name' => 'title',
                    'required' => true,
                    'filters' => array(
                        array(
                            'name' => 'alpha',
                        ),
                        array(
                            'name' => 'stringtrim',
                        ),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'stringlength',
                            'options' => array(
                                'min' => 3,
                                'max' => 80,
                            ),
                        ),
                    ),
        )));
        $this->add($factory->createInput(array(
                    'name' => 'url_string',
                    'required' => true,
                    'filters' => array(
                        array(
                            'name' => 'stringtrim'
                        ),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'stringlength',
                            'options' => array(
                                'min' => 3,
                                'max' => 80,
                            ),
                        ),
                    ),
        )));
        $this->add($factory->createInput(array(
                    'name' => 'content',
                    'required' => true,
        )));
    }

}
