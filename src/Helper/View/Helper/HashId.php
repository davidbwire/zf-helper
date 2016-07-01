<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorInterface;
use Hashids\Hashids;

/**
 * Description of HashId
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class HashId extends AbstractHelper
{

    /**
     *
     * @var string
     */
    private $salt;

    /**
     *
     * @var ServiceLocatorInterface
     */
    private $serviceLocator;

    /**
     *
     * @var Hashids
     */
    private $hashIds;

    public function __construct(ServiceLocatorInterface $helperPluginManager)
    {
        $this->serviceLocator = $helperPluginManager->getServiceLocator();
        $config = $this->serviceLocator->get('Config');
        if (!isset($config['hashids']['salt'])) {
            throw new \RuntimeException('Hashing salt has not been set.');
        }
        // we have a salt, persist it
        $this->salt = $config['hashids']['salt'];
        // create a new HashId instance
        $this->hashIds = new Hashids($this->salt);
    }
    /**
     *
     * @param string|array $param
     * @return string | \Helper\View\Helper\HashId
     */
    public function __invoke($param = null)
    {
        if ($param !== null) {
            return $this->hashIds->encode($param);
        }
        return $this;
    }
    /**
     *
     * @param string|array $param
     * @return string
     */
    public function encode($param)
    {
        return $this->hashIds->encode($param);
    }
    /**
     *
     * @param string $param
     * @return array
     */
    public function decode($param)
    {
        return $this->hashIds->decode($param);
    }

}
