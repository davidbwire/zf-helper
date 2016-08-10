<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Goalio\EventManager;

use Zend\Db\Adapter\Adapter;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\Sql\Sql;
use Helper\Mapper\UserMapperInterface;
use Zend\EventManager\Event;

/**
 * Description of Listener
 *
 * Listens to all GoalioForgotPassword Module events
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class GoalioForgotPasswordListener implements ServiceLocatorAwareInterface
{

    /**
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     *
     * @var Adapter
     */
    protected $dbAdapter;

    /**
     * Set user table
     *
     * @var string
     */
    protected $userTable = 'user';

    public function __construct(Adapter $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }

    /**
     * 
     * Ensure a login state is enabled for all users who recover their passwords
     * 
     * @param Event $e
     * @throws \RuntimeException
     */
    public function onResetPassword(Event $e)
    {
        $user = $e->getParam('user');
        // make sure we can change state to enableLogin incases of blocked users
        if (!is_object($user) || !method_exists($user, 'getId') || !method_exists($user,
                        'setState')) {
            throw new \RuntimeException('The parameter supplied of type' . gettype($user) . ' does not have a getId/setState method.');
        }
        // enable login
        $user->setState(1);
        // clear failed logins to prevent captcha firing
        $this->serviceLocator->get('HelperHistoryFailedLoginMapper')->resetFailedAttempts($user->getId());
    }

    /**
     *
     * @param string $table
     * @return Sql
     */
    protected function getSlaveSql($table)
    {
        return new Sql($this->dbAdapter, $table);
    }

    /**
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get create time
     * @return string
     */
    protected function getCreateTime()
    {
        $now = new \DateTime('now');
        return $now->format('Y-m-d H:i:s');
    }

}
