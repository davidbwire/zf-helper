<?php

namespace Helper\Form\View\Helper;

use Zend\Form\View\Helper\FormElementErrors as OriginalFormElementErrors;

/**
 * Description of FormElementErrors
 *
 * This class extends the original FormElementErrors class to allow us to create
 * our own custom error message styling.
 *
 * @author David Bwire
 */
class FormElementErrors extends OriginalFormElementErrors
{
    /*
     * @var string Templates for the open/close/separators for message tags
     */

    protected $messageCloseString = '</div></div>';
    protected $messageOpenFormat = '<div%s><div class="alert alert-danger form-element-error">';
    protected $messageSeparatorString = '</div><div class="help-inline">';

}
