<?php

namespace Helper\Form;

use Zend\Form\Form;

/**
 * Description of FileUploadForm
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class FileUploadForm extends Form
{

    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);

        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');

        $this->add([
            'name' => 'file',
            'type' => '\Zend\Form\Element\File',
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'class' => 'btn btn-sm btn-success',
                'value' => 'Upload',
            ],
            'options' => [
                'label' => 'Select file',
            ],
        ]);
    }

}
