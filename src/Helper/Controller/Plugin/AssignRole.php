<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Application\Entity\User;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;

/**
 * Description of AssignRole
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class AssignRole extends AbstractPlugin
{

    protected $dbAdapter;

    public function __construct(Adapter $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }

    /**
     *
     * @param \Application\Entity\User $user
     * @throws \Exception
     */
    public function add(User $user)
    {
        $userId = $user->getId();
        if (empty($userId)) {
            throw new \Exception('User id must be provided.');
        }
        $sql = $this->getSlaveSql();
        $roles = $user->getRole();
        foreach ($roles as $role) {
            $userId = (int) $user->getId();
            $roleId = (int) $role->getId();
            $hasRole = $this->hasRole($userId, $roleId);
            if (!$hasRole) {
                $insert = $sql->insert()
                        ->columns(array('user_id', 'role_id'))
                        ->values(array('user_id' => $userId, 'role_id' => $roleId));
                $sql->prepareStatementForSqlObject($insert)
                        ->execute();
            }
        }
    }

    /**
     *
     * @param int $userId
     * @param int $roleId
     */
    private function hasRole($userId, $roleId)
    {
        $sql = $this->getSlaveSql();
        $select = $sql->select();
        $select->where
                ->equalTo('user_id', $userId)
                ->and
                ->equalTo('role_id', $roleId);
        $results = $sql->prepareStatementForSqlObject($select)
                ->execute();
        if ($results->count()) {
            return true;
        }
        return false;
    }

    /**
     *
     * @return \Zend\Db\Sql\Sql
     */
    private function getSlaveSql()
    {
        $sql = new Sql($this->dbAdapter, 'user_has_role');
        return $sql;
    }

}
