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

/**
 * Description of SmsService
 *
 * @author Bitmarshals Digital <info@bitmarshals.co.ke>
 */
class Sms implements ServiceLocatorAwareInterface
{

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
     * @param type $to
     * @param type $message
     * @param type $from
     * @param type $params
     * @param type $gateway
     */
    public function send($to, $message, $from = '', $params = array(),
            $gateway = 'AfricasTalking')
    {

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
            $this->userNameInfobip = $aInfobip['userName'];
            $this->apiRootInfobip = $aInfobip['apiRoot'];
            $this->passwordInfobip = $aInfobip['password'];
        } else {
            // log
            $logger = $this->getServiceLocator()->get('LoggerService');
            $logger->err('Missing parameters to connect to infobip api.');
            // return error
            $response = new Response();
            return $response->setStatusCode(500)
                            ->setReasonPhrase('Missing parameters to connect to infobip api.');
        }
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

}
