<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Validator;

use Zend\Validator\AbstractValidator;

/**
 * Description of YearOfBirth
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class YearRange extends AbstractValidator
{

    const OUT_OF_RANGE = 'invalidYearOfBirth';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = [
        self::OUT_OF_RANGE => '%value%',
    ];

    /**
     *
     * @var array
     */
    protected $options = [
        'range' => 100,
        'valid_years' => 'past_to_present'
    ];

    public function isValid($value)
    {
        $this->setValue($value);

        $range = $this->getOption('range');
        $validYears = $this->getOption('valid_years');

        $currentYear = date('Y');

        switch ($validYears) {
            case 'past_to_present':
                $minYear = $currentYear - $range;
                $maxYear = $currentYear;
                break;
            case 'present_to_future':
                $minYear = $currentYear;
                $maxYear = $currentYear + $range;
                break;
            case 'past_to_future':
                $minYear = $currentYear - $range;
                $maxYear = $currentYear + $range;
                break;
            default:
                $minYear = $currentYear;
                $maxYear = $currentYear;
                break;
        }
        // the value should be within range
        $isValid = (($value >= $minYear) && ($value <= $maxYear));
        if (!$isValid) {
            $this->error(self::OUT_OF_RANGE,
                    'The year provided should be between ' .
                    $minYear . ' and ' . $maxYear);
        }
        return $isValid;
    }

}
