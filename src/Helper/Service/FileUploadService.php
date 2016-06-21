<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Service;

use Zend\Form\Form;
use InvalidArgumentException;
use Zend\Http\PhpEnvironment\Request;
use Zend\Filter;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;
use Zend\InputFilter\FileInput;
use Zend\Validator;
use Helper\Util\TokenGenerator;
use Zend\Validator\ValidatorInterface;
use Zend\Validator\File\MimeType;
use Zend\Validator\NotEmpty;
use Zend\Log\Logger;

/**
 * Description of UploadService
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class FileUploadService
{

    const UPLOAD_IMAGE = 'image';
    const UPLOAD_PDF = 'pdf';
    const UPLOAD_VIDEO = 'video';

    /**
     *
     * @var string
     */
    protected $uploadDirectory = './data/uploads/';

    /**
     *
     * @var InputFilter
     */
    protected $inputFilter;

    /**
     * An associative array errors encountered upon running the InputFilter
     *
     * @var array
     */
    protected $uploadErrors = [];

    /**
     *
     * @var ValidatorInterface[]
     */
    protected $customImageValidators = [];

    /**
     *
     * @var Logger
     */
    protected $logger;

    public function __construct(Logger $logger)
    {
        $this->inputFilter = new InputFilter();
        $this->logger = $logger;
    }

    /**
     *
     * @return array|false false  if upload fails
     */
    public function uploadImage()
    {
        return $this->upload(self::UPLOAD_IMAGE);
    }

    /**
     *
     * @param string $uploadType
     * @return array|false false  if upload fails
     */
    protected function upload($uploadType)
    {
        $tokenGenerator = new TokenGenerator($this->logger);
        $token = $tokenGenerator->generateToken();

        $fileInput = new FileInput('file');
        $fileInput->setRequired(true);
        // Note; File validators are run first then the file filters
        $target = $this->getUploadDirectory() . $token;

        $fileInput->getValidatorChain()
                ->attach(new Validator\File\UploadFile());
        // Note; file filters are run after file validation is complete
        $fileInput->getFilterChain()
                ->attach(new Filter\File\RenameUpload(array(
                    'target' => $target,
                    'randomize' => true,
                    'use_upload_extension' => true
        )));
        // determine the appropriate validator
        switch ($uploadType) {
            case self::UPLOAD_IMAGE:
                $this->attachImageValidators($fileInput);
                break;
            default:
                break;
        }
        $request = new Request();
        $inputFilter = $this->inputFilter;
        $inputFilter->add($fileInput)
                ->setData($request->getFiles()->toArray());

        // FileInput validators are run, but not the filters
        if ($inputFilter->isValid()) {
            // This is when the FileInput filters are run.
            /*
              ["file" => ['name' => 'Certificate-of-Incorporation.pdf',
              'type' => "application/x-pdf",
              "tmp_name" => "./data/uploads/logo_57687dc1069ae.pdf",
              "error" => 0,
              "size" => 492210]]
             *
             */
            return $inputFilter->getValues();
        } else {
            $errors = [];
            foreach ($inputFilter->getInvalidInput() as $error) {
                // add the error to element name for automatic error retreival
                // on the view layer
                $errors[$fileInput->getName()] = $error->getMessages();
            }
            // set errors to be picked later
            $this->uploadErrors = $errors;
            // indicate that the upload failed
            return false;
        }
    }

    /**
     * Return an indexed array of all errors that resulted from the upload
     * 
     * @return string[]
     */
    public function getUploadErrors()
    {
        return $this->uploadErrors;
    }

    /**
     *
     * @return string
     */
    public function getUploadDirectory()
    {
        return $this->uploadDirectory;
    }

    /**
     *
     * @param string $uploadDirectory
     * @return \Helper\Service\FileUploadService
     * @throws InvalidArgumentException
     */
    public function setUploadDirectory($uploadDirectory)
    {
        if (!is_readable($uploadDirectory)) {
            throw new \InvalidArgumentException('Please provide a valid '
            . 'directory."' . $uploadDirectory . '" provided.');
        }
        $this->uploadDirectory = $uploadDirectory;
        return $this;
    }

    /**
     * Attaches default image validators and any other custom validators that
     * have been supplied
     *
     * 
     * @param FileInput $fileInput
     * @return FileInput
     */
    protected function attachImageValidators(FileInput $fileInput)
    {
        $fileInputValidatorChain = $fileInput->getValidatorChain();
        // attach default validators
        $mimeTypeValidator = new MimeType();
        $mimeTypeValidator->setMimeType(['image/gif', 'image/jpeg', 'image/png']);
        $mimeTypeValidator->setMessage('Invalid file format. You are only allowed to upload '
                . '.jpeg, .gif and .png images', MimeType::FALSE_TYPE);
        $fileInputValidatorChain
                ->attach($mimeTypeValidator, true)
                // ensure we have the correct exensions as we are saving them
                ->attachByName(Validator\File\Extension::class,
                        ['png', 'gif', 'jpeg', 'jpg'], true);
        // check for any custom validators that may have been supplied
        foreach ($this->customImageValidators as $validator) {
            $fileInputValidatorChain
                    ->attach($validator);
        }
        return $fileInput;
    }

    /**
     *
     * @param ValidatorInterface[] $customImageValidators
     * @return \Helper\Service\FileUploadService
     */
    public function setCustomImageValidators(array $customImageValidators)
    {
        $this->customImageValidators = $customImageValidators;
        return $this;
    }

}
