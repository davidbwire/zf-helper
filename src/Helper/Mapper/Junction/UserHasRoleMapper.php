<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Mapper\Junction;

use Helper\Mapper\TableGateway;
use Zend\Db\Sql\Select;

/**
 * Description of UserHasRoleMapper
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class UserHasRoleMapper extends TableGateway
{
    /**
     *
     * @var array
     */
    protected $roles = [];

    /**
     *
     * @param int $userId
     * @param string $roleId
     * @return boolean
     */
    public function hasRole($userId, $roleId)
    {
        $sql = $this->getSlaveSql();
        $select = $sql->select()
                ->columns(array('user_id', 'role_id'))
                ->where(array('user_id' => $userId, 'role_id' => $roleId));
        $results = $sql->prepareStatementForSqlObject($select)
                ->execute();
        if ($results->count()) {
            return true;
        }
        return false;
    }

    /**
     *
     * @param int $userId
     * @param string $roleName
     * @return boolean
     *
     */
    public function userHasRoleName($userId, $roleName)
    {
        if (count($this->roles)) {
            return in_array($roleName, $this->roles);
        }
        $sql = $this->getSlaveSql();
        $select = $sql->select()
                ->columns(array('user_id', 'role_id'))
                ->where(array('user_id' => $userId))
                ->join('role', 'user.role_id=role.id', array('name'),
                Select::JOIN_INNER);
        $results = $sql->prepareStatementForSqlObject($select)
                ->execute();
        foreach ($results as $row) {
            $this->roles[] = $row['name'];
        }
        if (!count($this->roles)) {
            return false;
        }
        return in_array($roleName, $this->roles);
    }

}
