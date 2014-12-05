<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Mapper;

use Helper\Mapper\TableGateway;

/**
 * Description of PageMapper
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class PageMapper extends TableGateway
{
    public function __construct($table,
            \Zend\Db\Adapter\AdapterInterface $adapter, $features = null,
            \Zend\Db\ResultSet\ResultSetInterface $resultSetPrototype = null,
            \Zend\Db\Sql\Sql $sql = null)
    {
        parent::__construct($table, $adapter, $features,
                $resultSetPrototype, $sql);
    }

    public function fetchByUrlString($urlString)
    {
        $select = $this->getSql()->select();
        $select->where(array(
            'url_string' => $urlString,
        ));
        return $this->selectWith($select)->current();
    }

}
