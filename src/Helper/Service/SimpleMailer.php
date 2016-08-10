<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Service;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Log\LoggerInterface;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Mime;
use RuntimeException;
use Zend\View\Model\ViewModel;
use Zend\Mvc\View\Http\ViewManager;
use Zend\Mime\Message as MimeMessage;
use Exception;
use InvalidArgumentException;

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

    /**
     *
     * @var \Zend\Mime\Message
     */
    protected $attachmentsCarrier;

    /**
     *
     * @var ViewManager
     */
    protected $viewManager;

    public function __construct($config, ViewManager $viewManager,
            LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->viewManager = $viewManager;

        if (!isset($config['simple_mailer'])) {
            $this->logger->crit('Simple mailer is not set.');
            throw new \RuntimeException('Simple mailer is not configured'
            . ' properly.');
        }
        $this->smtpOptions = $config['simple_mailer']['smtp_options'];
        $this->accounts = $config['simple_mailer']['accounts'];
        $this->attachmentsCarrier = new MimeMessage();
    }

    /**
     * @param string|Address\AddressInterface|array|AddressList|Traversable $recipientEmailOrAddressOrList
     * @param string $emailSubject
     * @param null|string|\Zend\Mime\Message|object $emailBody
     * @param string $account the email account to email from default - default
     * @return Message|void
     */
    public function generateEmailMessage($recipientEmailOrAddressOrList,
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

    /**
     *
     * Given a file path, file name and it's mime type; this method adds the file
     * to the attachments carrier (Zend\Mime\Message)
     *
     * @param string $filePath eg /var/www/my-file.jpg
     * @param string $fileName eg image-file-name.jpg
     * @param string $mimeType eg image/jpg
     * @param string $encoding
     * @param string $disposition
     * @return MimeMessage
     * @throws RuntimeException
     */
    public function attachFile($filePath, $fileName, $mimeType,
            $encoding = Mime::ENCODING_BASE64,
            $disposition = Mime::DISPOSITION_ATTACHMENT)
    {
        if (!is_readable($filePath)) {
            throw new RuntimeException($filePath . ' is not readable.');
        }
        $fileContent = fopen($filePath, 'r');
        $fileAttachment = new MimePart($fileContent);
        $fileAttachment->setFileName($fileName)
                ->setType($mimeType)
                // Setting the encoding is recommended for binary data
                ->setEncoding($encoding)
                // sets file to be downloaded??
                ->setDisposition($disposition);

        $this->attachmentsCarrier->addPart($fileAttachment);
        return $this->attachmentsCarrier;
    }

    /**
     * Processes a view script and adds it to the attachments carrier
     *
     * @param  ViewModel $viewModelWithTemplate The ViewModel must have the template
     * as an option in order to be valid.
     *
     * @param  null|array|Traversable $viewModelValues Values to use when rendering. If none
     * provided, uses those in the composed variables container.
     * @param ViewManager $viewManager Ensure it has view resolvers
     * @return MimeMessage
     * @throws Exception\DomainException if a ViewModel is passed, but does not
     *                                   contain a template option.
     * @throws Exception\InvalidArgumentException if the values passed are not
     *                                            an array or ArrayAccess object
     * @throws Exception\RuntimeException if the template cannot be rendered
     * @throws InvalidArgumentException
     */
    public function attachHtmlFromViewModel(ViewModel $viewModelWithTemplate,
            $viewModelValues = null, ViewManager $viewManager = null)
    {
        if ($viewManager === null) {
            $viewManager = $this->viewManager;
        }
        if ($viewModelValues !== null) {
            throw new InvalidArgumentException('View model values cannot be '
            . 'set at the momement. Feature not implemented.');
        }
        $phpRendererWithResolver = $viewManager->getRenderer();
        $viewModelWithTemplate->setTerminal(false);
        $htmlContent = $phpRendererWithResolver
                ->render($viewModelWithTemplate);

        return $this->attachHtmlText($htmlContent);
    }

    /**
     * Pass in a html string and get it back as an attachment
     * 
     * @param string $htmlContent eg <p>hello world!</p>
     * @return MimeMessage
     */
    public function attachHtmlText($htmlContent)
    {
        if (gettype($htmlContent) !== 'string') {
            throw new \InvalidArgumentException(
            '$htmlContent variable provided should be a string.');
        }
        $mimedHtmlContent = new MimePart($htmlContent);
        $mimedHtmlContent->type = Mime::TYPE_HTML;
        $this->attachmentsCarrier
                ->addPart($mimedHtmlContent);
        return $this->attachmentsCarrier;
    }

}
