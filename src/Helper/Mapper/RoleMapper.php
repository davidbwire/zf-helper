<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Mapper;

use Helper\Mapper\TableGateway;
use Application\Entity\Role;

/**
 * Description of RoleMapper
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class RoleMapper extends TableGateway
{

    /**
     *
     * @param string $name
     * @return null|Role
     */
    public function getRoleByName($name)
    {
        $select = $this->getSlaveSql()
                ->select()
                ->columns(array('id'))
                ->where(array('name' => $name));
        $result = $this->selectWith($select);
        if ($result->count()) {
            return $result->current();
        } else {
            return null;
        }
    }
    /**
     *
     * @param string $name
     * @return int or null
     */
    public function getRoleIdByName($name)
    {
        $sql = $this->getSlaveSql();
        $select = $sql
                ->select()
                ->columns(array('id'))
                ->where(array('name' => $name));
        $result = $sql->prepareStatementForSqlObject($select)
                ->execute();
        if ($result->count()) {
            return $result->current()['id'];
        } else {
            return null;
        }
    }

}
