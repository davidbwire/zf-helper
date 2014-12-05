<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Mapper\Junction;

use Helper\Mapper\TableGateway;

/**
 * Description of UserHasRoleMapper
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class UserHasRoleMapper extends TableGateway
{

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

}
