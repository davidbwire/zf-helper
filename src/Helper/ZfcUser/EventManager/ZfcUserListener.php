<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\ZfcUser\EventManager;

use Zend\Db\Adapter\Adapter;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcUser\Authentication\Adapter\AdapterChainEvent;
use Zend\EventManager\Event;
use Zend\Http\PhpEnvironment\RemoteAddress;
use Zend\Http\PhpEnvironment\Request;
use Zend\Db\Sql\Sql;
use Zend\Captcha\ReCaptcha;
use Zend\Form\Element\Captcha;
use ZfcUser\Form\LoginFilter;
use ZfcUser\Form\Login as LoginForm;
use Zend\Validator\EmailAddress as EmailValidator;
use Zend\Session\Container;
use Application\Mapper\UserMapper;
use Helper\Mapper\HistoryFailedLoginMapper;
use Ramsey\Uuid\Uuid;

/**
 * Description of Listener
 *
 * Listens to all zfcuser module events
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class ZfcUserListener implements ServiceLocatorAwareInterface
{

    /**
     *
     * @var string
     */
    protected $recaptchaPublicKey;

    /**
     *
     * @var string
     */
    protected $recaptchaPrivateKey;

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
     * Set login history table
     * 
     * @var string
     */
    protected $historyLoginTable = 'history_login';

    /**
     * Set failed login history table
     * 
     * @var string
     */
    protected $historyFailedLoginTable = 'history_failed_login';

    /**
     * Set user table
     * 
     * @var string
     */
    protected $userTable = 'user';

    /**
     *
     * @var LoginForm
     */
    protected $loginForm;

    /**
     *
     * @var LoginFilter
     */
    protected $loginFilter;

    /**
     *
     * @var int
     */
    protected $userId;

    /**
     *
     * @var int
     */
    protected $failedLoginAttempts;

    /**
     *
     * @var HistoryFailedLoginMapper
     */
    protected $historyFailedLoginMapper;

    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->dbAdapter = $serviceLocator->get('dbAdapter');
        $config = $serviceLocator->get('Config');

        if (!isset($config['recaptcha']['public_key']) || !isset($config['recaptcha']['private_key'])) {
            throw new \RuntimeException('Check that the recaptcha private and public keys are correctly set.');
        }
        // set public key
        $this->recaptchaPublicKey = $config['recaptcha']['public_key'];
        // set private key
        $this->recaptchaPrivateKey = $config['recaptcha']['private_key'];
        $this->setServiceLocator($serviceLocator);
    }

    /**
     * Listen to authenticate.success event of \ZfcUser\Authentication\Adapter\AdapterChain
     * 
     * @param AdapterChainEvent $e
     */
    public function onAuthenticateSuccess(AdapterChainEvent $e)
    {

        $data = array();
        $remoteAddress = new RemoteAddress();
        $request = new Request();
        $data['user_id'] = $e->getIdentity();
        $data['authentication_code'] = $e->getCode();
        $data['ip_address'] = $remoteAddress->getIpAddress();
        $data['user_agent'] = $request->getServer('HTTP_USER_AGENT');
        $data['messages'] = json_encode($e->getMessages());
        $data['create_time'] = time();
        $data['id'] = Uuid::uuid4()->toString();
        $sql = $this->getSlaveSql($this->historyLoginTable);
        $insert = $sql->insert()->values($data);
        try {
            // reset failed login attempts to 0 if the record exists
            $this->resetFailedAttempts($data['user_id']);
            // record the login
            $sql->prepareStatementForSqlObject($insert)->execute();
        } catch (\Exception $exc) {
            $this->getLogger()->crit($exc->getTraceAsString());
        }
    }

    /**
     * Listen to authenticate.fail event of \ZfcUser\Authentication\Adapter\AdapterChain
     * 
     * @param AdapterChainEvent $e
     */
    public function onAuthenticateFail(AdapterChainEvent $e)
    {
        $data = array();
        // access email and manually get user_id
        $data['username_or_email'] = $e->getRequest()->getPost('identity');
        $data['messages'] = json_encode($e->getMessages());
        $data['authentication_code'] = $e->getCode();
        $data['create_time'] = time();
        $data['id'] = Uuid::uuid4()->toString();

        $sqlHistFailedLogin = $this->getSlaveSql($this->historyFailedLoginTable);
        try {
            $sqlUser = $this->getSlaveSql($this->userTable);

            // try retreiving the user_id to save it against the failed login
            $selectUser = $sqlUser->select()->columns(array('id'))
                    ->where(['email' => $data['username_or_email']])
                    ->where(['username' => $data['username_or_email']],
                            \Zend\Db\Sql\Predicate\PredicateSet::OP_OR)
                    ->limit(1);
            $resultSelectUser = $sqlUser->prepareStatementForSqlObject($selectUser)
                    ->execute();
            if ($resultSelectUser->count()) {
                $userId = $resultSelectUser->current()['id'];
                $failedAttempts = $this->getFailedAttempts($userId);
                if ($failedAttempts !== null) {
                    $data['attempts'] = $failedAttempts + 1;
                    $query = $sqlHistFailedLogin->update()->set($data)->where(array(
                        'user_id' => $userId));
                } else {
                    // first_failed login
                    $data['attempts'] = 1;
                    $data['user_id'] = $userId;
                    $data['create_time'] = time();
                    $query = $sqlHistFailedLogin->insert()->values($data);
                }
                $sqlHistFailedLogin->prepareStatementForSqlObject($query)->execute();
            }
        } catch (\Exception $ex) {
            $this->getLogger()->crit($ex->getTraceAsString());
        }
    }

    /**
     * Listen to the init event of \ZfcUser\Form\Login
     * 
     * @param Event $e
     */
    public function onLoginFormInit(Event $e)
    {

        // check the attempted login by the user, if >= 4 enable captcha
        // if >= 10 set user.state = 0
        $target = $e->getTarget();
        if ($target instanceof LoginForm) {
            $this->resetSessionEmail();
            $this->loginForm = $target;
            $userId = $this->getUserId();
            if ($userId !== null) {
                $failedAttempts = $this->getFailedAttempts($userId);
                if ($failedAttempts > 2) {
                    $recaptcha = new ReCaptcha();
                    // @todo pull settings from config
                    $recaptcha->setPrivkey($this->recaptchaPrivateKey)
                            ->setPubkey($this->recaptchaPublicKey);
                    $captcha = new Captcha('captcha');
                    $captcha->setCaptcha($recaptcha);
                    $captcha->setLabel('Please verify you are human');
                    $this->loginForm->add($captcha);
                }
            }
        }
    }

    /**
     * Listen to the init event of \ZfcUser\Form\LoginFilter
     *
     * @param Event $e
     */
    public function onLoginFilterInit(Event $e)
    {
        $target = $e->getTarget();
        if ($target instanceof LoginFilter) {
            // captcha is not required by default
            $required = false;
            $this->loginFilter = $target;
            $userId = $this->getUserId();
            // check we are able to get a user_id
            if ($userId !== null) {
                $failedAttempts = $this->getFailedAttempts($userId);
                if ($failedAttempts > 4) {
                    $this->disableLogin($userId);
                    $required = true;
                    $this->loginFilter->add(array('name' => 'captcha', 'required' => $required));
                } elseif ($failedAttempts > 2) {
                    $required = true;
                    $this->loginFilter->add(array('name' => 'captcha', 'required' => $required));
                }
            }
        }
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
     *
     * @param int $userId
     * @return int|null
     */
    protected function getFailedAttempts($userId)
    {
        if (!$this->failedLoginAttempts) {
            $failedAttempts = $this->getHistoryFailedLoginMapper()->getFailedAttempts($userId);
            $this->failedLoginAttempts = $failedAttempts;
        }
        return $this->failedLoginAttempts;
    }

    /**
     * Set $this->historyFailedLoginTable.attempts = 0
     * @param int $userId
     * @return null|int
     */
    public function resetFailedAttempts($userId)
    {
        return $this->getHistoryFailedLoginMapper()
                        ->resetFailedAttempts($userId);
    }

    /**
     * Retreive email from session and try getting a user_id
     * 
     * @return int|null
     */
    protected function getUserId()
    {
        // check if we already have a user_id
        if (!$this->userId) {
            $userId = null;
            $session = new Container('ZfcUserListener');
            // email was validated before being added
            $email = $session->email;
            if ($email) {
                $sql = $this->getSlaveSql($this->userTable);
                $select = $sql->select()
                        ->columns(array('id'))
                        ->where(array('email' => $email))
                        ->limit(1);
                $result = $sql->prepareStatementForSqlObject($select)->execute();
                if ($result->count()) {
                    $userId = $result->current()['id'];
                }
            }
            // persist for reuse
            $this->userId = $userId;
        }

        return $this->userId;
    }

    /**
     * Persist in a session the most recent email received via post
     */
    protected function resetSessionEmail()
    {
        $service = $this->serviceLocator->get('Request');
        if ($service instanceof Request) {
            // retreive email from post request
            $identity = $service->getPost('identity', false);
            $session = new Container('ZfcUserListener');
            // check we have a valid email
            $emailValidator = new EmailValidator();
            if ($identity && $emailValidator->isValid(trim($identity))) {
                $email = trim($identity);
                $session->email = $email;
            }
        }
    }

    /**
     * Login attempts have been too many and should thus be blocked for client
     * to recover password
     * 
     * @param null|int $userId
     */
    protected function disableLogin($userId)
    {
        $userMapper = $this->serviceLocator->get('applicationUserMapper');
        if (!$userMapper instanceof UserMapper) {
            return null;
        }
        return $userMapper->disableLogin($userId);
    }

    /**
     *
     * @return HistoryFailedLoginMapper
     */
    public function getHistoryFailedLoginMapper()
    {
        if (!$this->historyFailedLoginMapper) {
            $this->setHistoryFailedLoginMapper($this->serviceLocator->get('helperHistoryFailedLoginMapper'));
        }
        return $this->historyFailedLoginMapper;
    }

    /**
     *
     * @param HistoryFailedLoginMapper $historyFailedLoginMapper
     * @return \Helper\ZfcUser\EventManager\ZfcUserListener
     */
    public function setHistoryFailedLoginMapper(HistoryFailedLoginMapper $historyFailedLoginMapper)
    {
        $this->historyFailedLoginMapper = $historyFailedLoginMapper;
        return $this;
    }

    /**
     * Get create time
     * @return string
     */
    private function getCreateTime()
    {
        $now = new \DateTime('now');
        return $now->format('Y-m-d H:i:s');
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
     * @return \Zend\Log\Logger
     */
    protected function getLogger()
    {
        return $this->serviceLocator->get('loggerService');
    }

    /**
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

}
