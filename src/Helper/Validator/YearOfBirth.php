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
class YearOfBirth extends AbstractValidator
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
    ];

    public function isValid($value)
    {
        $this->setValue($value);

        $range = $this->getOption('range');

        $currentYear = date('Y');
        $minYear = $currentYear - $range;

        $isValid = (($value <= $currentYear) && ($value >= $minYear));
        if (!$isValid) {
            $this->error(self::OUT_OF_RANGE,
                    'Your year of birth should be between ' .
                    $minYear . ' and ' . $currentYear);
        }
        return $isValid;
    }

}
