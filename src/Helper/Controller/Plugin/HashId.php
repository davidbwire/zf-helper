<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Hashids\Hashids;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Description of HashId
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class HashId extends AbstractPlugin
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

    public function __construct(ServiceLocatorInterface $controllerPluginManager)
    {

        $this->serviceLocator = $controllerPluginManager
                ->getServiceLocator();
        $config = $this->serviceLocator->get('Config');
        if (!isset($config['hashids']['salt'])) {
            $controllerPluginManager->get('logger')
                    ->crit('Hashing salt has not been set.');
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
