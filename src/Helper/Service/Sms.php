<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Service;

use Exception;
use Zend\Http\Client;
use Zend\Http\Client\Adapter\Curl;
use Zend\Http\Request;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Http\Response;
use Helper\AfricasTalking\AfricasTalkingGateway;

/**
 * Description of SmsService
 *
 * @author Bitmarshals Digital <info@bitmarshals.co.ke>
 */
class Sms implements ServiceLocatorAwareInterface
{

    /**
     *
     * @var array|Response
     */
    protected $result;

    /**
     *
     * @var string
     */
    protected $apiRootInfobip;

    /**
     *
     * @var string
     */
    protected $userNameInfobip;

    /**
     *
     * @var string
     */
    protected $passwordInfobip;

    /**
     * 
     *
     * @var string
     */
    protected $defaultSenderIdInfobip;

    /**
     *
     * @var array
     */
    protected $config;

    /**
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     *
     * @param string $to
     * @param string $message
     * @param string $from
     * @param array $params
     * @param string $gateway
     */
    public function send($to, $message, $from = null, $params = array(),
            $gateway = 'AfricasTalking')
    {
        switch ($gateway) {
            case 'AfricasTalking':
                return $this->sendSmsViaAfricasTalkingGateway($to, $message,
                                $from);
            case 'Infobip':
                return $this->sendSingleSmsInfobip($to, $message, $from);
            default:
                return $this->sendSmsViaAfricasTalkingGateway($to, $message,
                                $from);
        }
    }

    /**
     *
     * @param string $to
     * @param string $message
     * @param string $from
     * @param array $params
     * @return type
     * @throws Exception
     */
    private function sendSmsViaAfricasTalkingGateway($to, $message,
            $from = null, $params = array())
    {
        $config = $this->getConfig();
        if (isset($config['mobichurch']['africas_talking'])) {
            $at = $config['mobichurch']['africas_talking'];
            $username = $at['username'];
            $apiKey = $at['apiKey'];
            $senderId = $at['senderId'] ? $at['senderId'] : null;
            $shortCode = $at['shortCode'] ? $at['shortCode'] : null;
            if ($from === null) {
                if (!empty($shortCode)) {
                    $from = $shortCode;
                } elseif (!empty($senderId)) {
                    $from = $senderId;
                }
            }
            // instantiate afrcias talking gateway
            $africasTalkingGateway = new AfricasTalkingGateway($username,
                    $apiKey);
            // send message
            try {
                $result = $africasTalkingGateway->sendMessage($to, $message,
                        $from);
                $this->result = $result;
                return $this;
            } catch (Exception $exc) {
                $this->getServiceLocator()->get('LoggerService')
                        ->crit($exc->getMessage() . ' ' . __FILE__ . ' ' . __LINE__);
            }
        } else {
            // log
            $logger = $this->getServiceLocator()->get('LoggerService');
            $logger->err('Missing parameters to connect to infobip api.');
            throw new Exception('Missing parameters to connect to AfricasTalking gateway.');
        }
    }

    /**
     *
     * @param int $to
     * @param string $message
     * @param string $from
     * @return \Zend\Http\Response
     */
    public function sendSingleSmsInfobip($to, $message, $from = '')
    {
        // initialize variables
        $result = $this->initializeInfobipVariables();
        if ($result instanceof Response) {
            // an error occured
            return $result;
        }
        if (empty($from)) {
            $from = ($this->defaultSenderIdInfobip ? $this->defaultSenderIdInfobip : 'IBSMS');
        }
        // connect to infobip and return the response
        $httpClient = new Client($this->apiRootInfobip . '/text/single',
                array('timeout' => 30));
        $httpClient->setRawBody(json_encode(array('from' => $from,
            'to' => $to, 'text' => $message, 'singleShift' => true, 'lockingShift' => false,
            'languageCode' => 'EN', 'type' => 'DEFAULT')));
        $httpClient->setAuth($this->userNameInfobip, $this->passwordInfobip,
                        Client::AUTH_BASIC)
                ->setHeaders(array('Content-Type: application/json',
                    'Accept: application/json'))
                ->setAdapter(new Curl())
                ->setMethod(Request::METHOD_POST);
        $response = $httpClient->send();
        return $response;
    }

    /**
     * Initialize class variables
     * 
     * @return Response|void
     */
    protected function initializeInfobipVariables()
    {
        $config = $this->getConfig();
        if (isset($config['mobichurch']['infobip'])) {
            $aInfobip = $config['mobichurch']['infobip'];
            $this->defaultSenderIdInfobip = $aInfobip['defaultSenderId'];
            $this->userNameInfobip = $aInfobip['username'];
            $this->apiRootInfobip = $aInfobip['apiRoot'];
            $this->passwordInfobip = $aInfobip['password'];
        } else {
            // log
            $logger = $this->getServiceLocator()->get('LoggerService');
            $logger->err('Missing parameters to connect to infobip api.');
            // throw error
            throw new Exception('Missing parameters to connect to infobip api.');
        }
    }

    /**
     *
     * @return \Zend\Log\Logger
     */
    protected function getLogger()
    {
        return $this->getServiceLocator()->get('LoggerService');
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
     * @return \Application\Service\SmsService
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getConfig()
    {
        if (!$this->config) {
            $this->config = $this->getServiceLocator()->get('Config');
        }
        return $this->config;
    }

    /**
     *
     * @param string $gateway
     * @return boolean
     */
    public function isSuccess($gateway = 'AfricasTalking')
    {
        if ($gateway == 'AfricasTalking') {
            $isSuccessful = false;
            foreach ($this->result as $userResult) {
                // iterate all results if all successful then it was a success
                $isSuccessful = ($userResult->status == 'Success');
            }
            return $isSuccessful;
        } elseif ($gateway == 'Infobip') {
            return $this->result->isSuccess();
        }
        return false;
    }

    /**
     *
     * @return array|Response
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     *
     * @param array|Response $result
     * @return \Helper\Service\Sms
     */
    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }

}
