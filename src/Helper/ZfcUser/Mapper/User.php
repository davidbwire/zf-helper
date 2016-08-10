<?php

namespace Helper\ZfcUser\Mapper;

use Zend\Stdlib\Hydrator\HydratorInterface;
use ZfcUser\Mapper\User as ZfcUser;

class User extends ZfcUser
{

    protected $tableName = 'user';

    public function findById($id)
    {
        $select = $this->getSelect()
                ->where(array('id' => $id));

        $entity = $this->select($select)->current();
        $this->getEventManager()->trigger('find', $this,
                array('entity' => $entity));
        return $entity;
    }

    public function update($entity, $where = null, $tableName = null,
            HydratorInterface $hydrator = null)
    {
        if (!$where) {
            $where = ['id' => $entity->getId()];
        }

        return parent::update($entity, $where, $tableName, $hydrator);
    }

}
