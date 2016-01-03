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
     * @deprecated since version number
     * @return boolean
     */
    public function hasRole($userId, $roleId)
    {
        $sql = $this->getSlaveSql();
        $select = $sql->select()
                ->columns(array('user_id', 'role_id'))
                ->where(array('user_id' => $userId, 'role_id' => $roleId));
        $result = $sql->prepareStatementForSqlObject($select)
                ->execute();
        if ($result->count()) {
            return true;
        }
        return false;
    }

    public function userHasRoleId($userId, $roleId)
    {
        $sql = $this->getSlaveSql();
        $select = $sql->select()
                ->columns(array('user_id', 'role_id'))
                ->where(array('user_id' => $userId, 'role_id' => $roleId));
        $result = $sql->prepareStatementForSqlObject($select)
                ->execute();
        if ($result->count()) {
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
                ->join('role', 'user_has_role.role_id=role.id', array('name'),
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
    /**
     * Grant a specific role id to user
     * 
     * @param int $userId
     * @param int $roleId
     * @return boolean
     */
    public function grantRole($userId, $roleId)
    {
        $sql = $this->getSlaveSql();
        $insert = $sql->insert()
                ->columns(array('user_id', 'role_id'))
                ->values(array('user_id' => $userId, 'role_id' => $roleId));
        $result = $sql->prepareStatementForSqlObject($insert)
                ->execute();
        if ($result->count()) {
            return $this->userHasRoleId($userId, $roleId);
        }
        return false;
    }

}
