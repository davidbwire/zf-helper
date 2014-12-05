<?php

namespace Helper\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods;
use Helper\Entity\Base as BaseEntity;

/**
 * Description of BaseFieldset
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class BaseFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct($name = 'base', $options = array())
    {
        parent::__construct($name, $options);
        $this->setHydrator(new ClassMethods(true));
        $this->setObject(new BaseEntity());
        $this->add(array(
            'type' => 'hidden',
            'name' => 'id',
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            'id' => array(
                'required' => true,
            )
        );
    }

}
