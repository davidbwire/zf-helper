<?php

namespace Helper\Zend\Form;

use Zend\Form\Form as ZendForm;

/**
 * Description of Form
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class Form extends ZendForm
{

    public function removeValidator($name)
    {
        foreach ($this->validators as $key => $element) {
            $validator = $element['instance'];
            if ($validator instanceof $name) {
                unset($this->validators[$key]);
                break;
            }
        }
    }

}
