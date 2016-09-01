<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Service;

use Exception;
use Zend\Http\Client;
use Zend\Http\Client\Adapter\Curl;
use Zend\Http\Request;
use Zend\Http\Response;
use Helper\AfricasTalking\AfricasTalkingGateway;
use Zend\Log\Logger;

/**
 * Description of SmsService
 *
 * @author Bitmarshals Digital <info@bitmarshals.co.ke>
 */
class Sms
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
     * @var Logger
     */
    protected $logger;

    /**
     *
     * @param string $to
     * @param string $message
     * @param string $use senderId|shortCode
     * @param array $params linkId, etc
     * @param string $gateway
     */
    public function send($to, $message, $use = 'senderId',
            array $params = array(), $gateway = 'AfricasTalking')
    {
        switch ($gateway) {
            case 'AfricasTalking':
                return $this->sendSmsViaAfricasTalkingGateway($to, $message,
                                $use, $params);
            case 'Infobip':
                return $this->sendSingleSmsInfobip($to, $message);
            default:
                return $this->sendSmsViaAfricasTalkingGateway($to, $message,
                                $use);
        }
    }

    /**
     *
     * @param string $to
     * @param string $message
     * @param string $use use senderId|shortCode
     * @param array $params
     * @return type
     * @throws Exception
     */
    private function sendSmsViaAfricasTalkingGateway($to, $message,
            $use = 'senderId', array $params = array())
    {
        $config = $this->getConfig();
        if (isset($config['app']['africas_talking'])) {
            $at = $config['app']['africas_talking'];
            $username = $at['username'];
            $apiKey = $at['apiKey'];
            $senderId = $at['senderId'] ? $at['senderId'] : null;
            $shortCode = $at['shortCode'] ? $at['shortCode'] : null;
            $from = null;
            if ($use == 'senderId') {
                if (!empty($senderId)) {
                    $from = $senderId;
                }
            } elseif ($use == 'shortCode') {
                if (!empty($shortCode)) {
                    $from = $shortCode;
                }
            }

            // instantiate afrcias talking gateway
            $africasTalkingGateway = new AfricasTalkingGateway($username,
                    $apiKey);
            // send message
            try {
                if ($use == 'senderId') {
                    // send message normally
                    $result = $africasTalkingGateway->sendMessage($to, $message,
                            $from);
                } else {
                    // send via short_code
                    $linkId = null;
                    if (array_key_exists('linkId', $params)) {
                        $linkId = $params['linkId'];
                    }
                    $result = $africasTalkingGateway->sendMessage($to, $message,
                            $from, 0, array('linkId' => $linkId));
                    try {
                        $this->getLogger()->error((array) $result);
                    } catch (Exception $exc) {
                        $this->getLogger()
                                ->crit('Code - ' . $exc->getCode() . ' Message - ' . $exc->getMessage() . ' ' . __FILE__ . ' ' . __LINE__);
                    }
                    $this->result = $result;
                    return $this;
                }
                $this->result = $result;
                return $this;
            } catch (Exception $exc) {
                $this->getLogger()
                        ->crit('Code - ' . $exc->getCode() . ' Message - ' . $exc->getMessage() . ' ' . __FILE__ . ' ' . __LINE__);
            }
        } else {
            // log
            $logger = $this->getLogger();
            $logger->err('Missing parameters to connect to AfricasTalking gateway.');
            throw new Exception('Missing parameters to connect to AfricasTalking gateway.');
        }
    }

    /**
     *
     * @param int $to
     * @param string $message
     * @return \Zend\Http\Response
     */
    public function sendSingleSmsInfobip($to, $message)
    {
        $from = null;
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
        if (isset($config['app']['infobip'])) {
            $aInfobip = $config['app']['infobip'];
            $this->defaultSenderIdInfobip = $aInfobip['defaultSenderId'];
            $this->userNameInfobip = $aInfobip['username'];
            $this->apiRootInfobip = $aInfobip['apiRoot'];
            $this->passwordInfobip = $aInfobip['password'];
        } else {
            // log
            $logger = $this->getLogger();
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
        return $this->logger;
    }

    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     *
     * @return array
     */
    protected function getConfig()
    {
        return $this->config;
    }

    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
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
