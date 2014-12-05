<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Application\Mapper\UserMapper;

/**
 * Description of UserName
 *
 * Get user's name(s)
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class UserName extends AbstractHelper
{

    protected $userMapper;
    protected $userId;

    public function __construct(UserMapper $userMapper)
    {
        $this->userMapper = $userMapper;
    }

    public function __invoke($userId,
            $columns = array('first_name', 'last_name'))
    {
        return $this->userMapper->getNameById($userId, $columns);
    }

}
