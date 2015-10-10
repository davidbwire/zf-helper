<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Service;

use Zend\Log\Logger as ZfLogger;
use Zend\Log\Writer\Stream;

/**
 * Description of Logger
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class Logger extends ZfLogger
{

    /**
     *
     * @var string
     */
    protected $file;

    /**
     *
     * @var string
     */
    protected $logPath = './data/logs/';

    /**
     *
     * @param string $file
     */
    public function __invoke($file)
    {
        if ($file) {
            $this->file = $file;
        }
    }

    public function __construct($options = null)
    {
        parent::__construct($options);

        $writer = new Stream($this->getWriterFile());
        $this->addWriter($writer);
    }

    /**
     *
     * @param string $file
     * @return string
     * @throws \RuntimeException
     */
    protected function getWriterFile($file = 'log')
    {
        if ($file) {
            $this->file = $file;
        }
        $writerFile = $this->logPath . $this->file . '.txt';
        if (is_writable($writerFile)) {
            return $writerFile;
        } else {
            throw new \RuntimeException('The provided file is not writable.');
        }
    }

    public function crit($message, $extra = array())
    {
        $writer = new Stream($this->getWriterFile('critical'));
        $this->addWriter($writer);
        parent::crit($message, $extra);
    }

    public function critical($message, $extra = array())
    {
        return $this->crit($message, $extra);
    }

    public function err($message, $extra = array())
    {
        $writer = new Stream($this->getWriterFile('error'));
        $this->addWriter($writer);
        parent::err($message, $extra);
    }

    public function error($message, $extra = array())
    {
        return $this->err($message, $extra);
    }

}
