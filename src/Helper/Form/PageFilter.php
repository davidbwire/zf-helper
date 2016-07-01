<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Form;

use Zend\InputFilter\InputFilter;
use Helper\Form\Fieldset\PageFieldsetFilter;

/**
 * Description of PageCreateFilter
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class PageFilter extends InputFilter
{
    public function __construct()
    {
        $this->add(new PageFieldsetFilter(), 'page');
    }

}
