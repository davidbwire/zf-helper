<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Validator;

use Zend\Validator\AbstractValidator;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberType;
use libphonenumber\PhoneNumber as LibPhoneNumber;

/**
 * Description of PhoneNumber
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class MobileNumber extends AbstractValidator
{

    const INVALID = 'phoneNumberInvalid';
    const INVALID_MOBILE_NUMBER = 'invalidMobileNumber';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = [
        self::INVALID => '%value%',
        self::INVALID_MOBILE_NUMBER => 'The number provided (%value%) is not a valid mobile number.'
    ];

    public function isValid($value)
    {
        $this->setValue($value);
        $isMobileNumber = false;
        $isValidNumber = false;
        try {
            // ensure the + sign to allow passing null to
            // $phoneNumberUtil->parse();
            if (preg_match('/^\+/', $value) === 0) {
                $value = '+' . $value;
            }
            $phoneNumberUtil = PhoneNumberUtil::getInstance();
            $phoneNumber = $phoneNumberUtil->parse($value, null);

            if ($phoneNumber instanceof LibPhoneNumber) {
                $isValidNumber = $phoneNumberUtil->isValidNumber($phoneNumber);
                $isMobileNumber = ($phoneNumberUtil
                                ->getNumberType($phoneNumber) ===
                        PhoneNumberType::MOBILE);
            }
            if ($isValidNumber && $isMobileNumber) {
                return true;
            } else {
                $this->error(self::INVALID_MOBILE_NUMBER);
            }
        } catch (NumberParseException $ex) {
            $this->error(self::INVALID, $ex->getMessage());
        }
        return false;
    }

}
