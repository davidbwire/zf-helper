<?php

namespace Helper\View\Helper;
use Zend\View\Helper\FlashMessenger as DefaultFlashMessenger;

/**
 * Description of FlashMessenger
 *
 * @author David Bwire
 */
class FlashMessenger extends DefaultFlashMessenger
{

    /**
     * Templates for the open/close/separators for message tags
     *
     * @var string
     */
    protected $messageCloseString = '</div></div>';
    protected $messageOpenFormat = '<div class="alert-messages"><div%s>
        <button type="button" class="close alert-close"><span class="text-white" aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>';
    protected $messageSeparatorString = '</div><div%s>
        <button type="button" class="close alert-close"><span class="text-white" aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>';

}
