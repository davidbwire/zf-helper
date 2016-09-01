<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Validator;

use Zend\Validator\AbstractValidator;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;

/**
 * Description of PhoneNumber
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class PhoneNumber extends AbstractValidator
{

    public function isValid($value)
    {
        // ensure the + sign to allow passing null to
        // $phoneNumberUtil->parse();
        if (preg_match('/^\+/', $value) === 0) {
            $value = '+' . $value;
        }
        $phoneNumberUtil = PhoneNumberUtil::getInstance();
        $phoneNumber = $phoneNumberUtil->parse($value, null);

        return $phoneNumberUtil->isValidNumber($phoneNumber);
    }

}
