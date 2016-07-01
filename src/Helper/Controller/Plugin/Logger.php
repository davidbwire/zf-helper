<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Log\Logger as ZfLogger;
use Zend\Log\LoggerInterface;

/**
 * Description of Logger
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class Logger extends AbstractPlugin implements LoggerInterface
{

    private $loggerService;

    public function __construct(ZfLogger $loggerService)
    {
        $this->loggerService = $loggerService;
    }

    public function alert($message, $extra = array())
    {
        return $this->loggerService->alert($message, $extra);
    }

    public function crit($message, $extra = array())
    {
        return $this->loggerService->crit($message, $extra);
    }

    public function debug($message, $extra = array())
    {
        return $this->loggerService->debug($message, $extra);
    }

    public function emerg($message, $extra = array())
    {
        return $this->loggerService->emerg($message, $extra);
    }

    public function err($message, $extra = array())
    {
        return $this->loggerService->err($message, $extra);
    }

    public function info($message, $extra = array())
    {
        return $this->loggerService->info($message, $extra);
    }

    public function notice($message, $extra = array())
    {
        return $this->loggerService->notice($message, $extra);
    }

    public function warn($message, $extra = array())
    {
        return $this->loggerService->warn($message, $extra);
    }

}
