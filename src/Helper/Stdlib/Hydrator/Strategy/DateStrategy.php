<?php

namespace Helper\Stdlib\Hydrator\Strategy;

use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;

/**
 * Description of DateStrategy
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class DateStrategy implements StrategyInterface
{

    public function extract($value)
    {
        $date = new \DateTime($value, new \DateTimeZone('Africa/Nairobi'));
        return $date->format('Y-m-d');
    }

    public function hydrate($value)
    {
        $date = new \DateTime($value, new \DateTimeZone('Africa/Nairobi'));
        return $date->format('Y-m-d');
    }

}
