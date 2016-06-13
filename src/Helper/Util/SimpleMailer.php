<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Util;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Log\LoggerInterface;
use Exception;

/**
 * Description of SimpleMailer
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class SimpleMailer
{

    /**
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     *
     * @var array
     */
    protected $smtpOptions;

    /**
     *
     * @var array
     */
    protected $accounts;

    public function __construct($config, LoggerInterface $logger)
    {
        $this->logger = $logger;

        if (!isset($config['simple_mailer'])) {
            $this->logger->crit('Simple mailer is not set.');
            throw new \RuntimeException('Simple mailer is not configured'
            . ' properly.');
        }
        $this->smtpOptions = $config['simple_mailer']['smtp_options'];
        $this->accounts = $config['simple_mailer']['accounts'];
    }

    /**
     * @param string|Address\AddressInterface|array|AddressList|Traversable $recipientEmailOrAddressOrList
     * @param string $emailSubject
     * @param null|string|\Zend\Mime\Message|object $emailBody
     * @param string $account
     * @return Message
     */
    public function preparePlainTextEmail($recipientEmailOrAddressOrList,
            $emailSubject, $emailBody, $account = 'default')
    {
        $mailingAccountDetail = $this->getMailingAccountDetail($account);
        // set username and password
        $this->smtpOptions['connection_config']['username'] = $mailingAccountDetail['username'];
        $this->smtpOptions['connection_config']['password'] = $mailingAccountDetail['password'];

        $emailMessage = new Message();
        $emailMessage->addTo($recipientEmailOrAddressOrList)
                ->setSubject($emailSubject)
                ->setBody($emailBody)
                ->addFrom($mailingAccountDetail['username'],
                        $mailingAccountDetail['name']);
        return $emailMessage;
    }

    /**
     *
     * @param Message $emailMessage
     * @param array|Traversable|null $smtpOptions
     */
    public function send(Message $emailMessage, $smtpOptions = null)
    {
        $transport = new Smtp();
        if ($smtpOptions === null) {
            $transport->setOptions(new SmtpOptions(
                    $this->smtpOptions));
        } else {
            $transport->setOptions(new SmtpOptions($smtpOptions));
        }
        $transport->send($emailMessage);
    }

    /**
     * Retreive details for a specific account
     * 
     * @param string $accountName
     * @return array
     * @throws Exception
     */
    private function getMailingAccountDetail($accountName)
    {
        if (!isset($this->accounts[$accountName])) {
            throw new \Exception('The account ' . $accountName . ' has not been '
            . 'configured.');
        }
        return $this->accounts[$accountName];
    }

}
