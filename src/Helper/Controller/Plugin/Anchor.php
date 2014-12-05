<?php

namespace Helper\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Description of Anchor
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class Anchor extends AbstractPlugin {

    /**
     *
     * @var string
     */
    protected $type = '';

    /**
     *
     * @var string
     */
    protected $href = '#';

    /**
     *
     * @var string
     */
    protected $class = '';

    /**
     *
     * @var string
     */
    protected $id = '';

    /**
     *
     * @var string
     */
    protected $template = '<a  href="%s" id="%s" class="%s" type="%s">%s</a>';

    public function __invoke($text = 'this is a link', array $data = array()) {

        if (array_key_exists('type', $data)) {
            $this->type = $data['type'];
        }
        if (array_key_exists('class', $data)) {
            $this->class = $data['class'];
        }
        if (array_key_exists('id', $data)) {
            $this->id = $data['id'];
        }
        if (array_key_exists('href', $data)) {
            $this->href = $data['href'];
        }
        if (array_key_exists('template', $data)) {
            $this->template = $data['template'];
        }
        return sprintf($this->template, $this->href, $this->id, $this->class, $this->type, $text);
    }

}

