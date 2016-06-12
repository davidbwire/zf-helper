<?php

namespace Helper\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of ImageUploaderForm
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class ImageUploadForm extends Form implements InputFilterProviderInterface
{

    public function __construct($name = 'upload_image', $options = [])
    {
        parent::__construct($name, $options);

        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->setInputFilter(new InputFilter());

        $this->add([
            'name' => 'image',
            'type' => '\Zend\Form\Element\File',
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'class' => 'btn btn-sm',
                'value' => 'Upload Image',
            ],
            'options' => [
                'label' => 'Choose an image',
            ],
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'image' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\File\RenameUpload',
                        'options' => [
                            'target' => './public/logo',
                            'randomize' => false,
                            'overwrite' => true,
                            'use_upload_name' => true,
                        ],
                    ],
                ],
                'validators' => [
//                    [
//                        'name' => '\Zend\Validator\File\IsImage',
//                    ],
                    [
                        'name' => '\Zend\Validator\File\ImageSize',
                        'options' => [
                            'minWidth' => 100, 'minHeight' => 65,
                            'maxWidth' => 100, 'maxHeight' => 65,
                        ],
                    ],
//                    [
//                        'name' => '\Zend\Validator\File\Extension',
//                        'options' => [
//                            'extension' => 'jpg,jpeg,png',
//                            'messages' => [
//                                \Zend\Validator\File\Extension::FALSE_EXTENSION => 'upload jpg,jpeg,png formats  only',
//                            ],
//                        ],
//                    ],
                ],
            ],
        ];
    }

}
