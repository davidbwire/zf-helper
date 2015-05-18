<?php

namespace Helper\BjyAuthorize\Provider\Identity;

use BjyAuthorize\Provider\Identity\ZfcUserZendDb;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Sql;
use ZfcUser\Service\User;
use Zend\Db\Adapter\Adapter;

/**
 * Description of BitZfcUserZendDb
 *
 * Overrides ZfcUserZendDb to provide a custom table name
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class BitZfcUserZendDb extends ZfcUserZendDb
{

    protected $tableName = 'user_has_role';

    public function __construct(Adapter $adapter, User $userService)
    {
        parent::__construct($adapter, $userService);
        $this->adapter = $adapter;
        $this->userService = $userService;
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentityRoles()
    {
        $authService = $this->userService->getAuthService();

        if (!$authService->hasIdentity()) {
            return array($this->getDefaultRole());
        }

        // get roles associated with the logged in user
        $sql = new Sql($this->adapter);
        $select = $sql->select()->from('user_has_role');
        $select->where(array('user_id' => $authService->getIdentity()->getId()))
                ->join('role', 'user_has_role.role_id=role.id',
                        array('role_name' => 'name'),
                        $select::JOIN_INNER);
        $results = $sql->prepareStatementForSqlObject($select)->execute();
        $roles = array();

        foreach ($results as $i) {
            // bugfix use role_name instead of role_id
            $roles[] = $i['role_name'];
        }

        return $roles;
    }

}
