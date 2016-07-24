<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Helper\Mapper\UserMapperInterface;

/**
 * Description of UsersName
 *
 * Get user's name(s)
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class UsersName extends AbstractHelper
{

    /**
     *
     * @var object
     */
    protected $userMapper;

    /**
     *
     * @var string|int
     */
    protected $userId;

    /**
     * Avoid type check on constructor injection for flexibility pr
     * @param object $userMapper
     */
    public function __construct(UserMapperInterface $userMapper)
    {
        $this->userMapper = $userMapper;
    }

    public function __invoke($userId,
            $columns = array('first_name', 'last_name'))
    {
        return $this->userMapper->getNameById($userId, $columns);
    }

}
