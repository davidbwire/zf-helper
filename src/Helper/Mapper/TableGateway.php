<?php

namespace Helper\Mapper;

use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway as ZfTableGateway;
use Zend\Db\Sql\Predicate\Predicate;
use Zend\Db\Adapter\Adapter;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Log\Logger;
use ZF\ApiProblem\ApiProblemResponse;
use ZF\ApiProblem\ApiProblem;
use Zend\Http\Response;

/**
 * Description of AbstractTableGateway
 *
 * @author David Bwire
 */
class TableGateway extends ZfTableGateway implements ServiceLocatorAwareInterface
{

    /**
     *
     * @var Logger
     */
    private $logger;

    /**
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Retreive an Sql instance preset with the dbAdapter and tableName
     *
     * @return \Zend\Db\Sql\Sql $sql
     */
    public function getSlaveSql($table = null)
    {
        if (!empty($table)) {
            return new Sql($this->getAdapter(), $table);
        }
        return new Sql($this->getAdapter(), $this->getTable());
    }

    /**
     * @return \Zend\Db\Sql\Predicate\Predicate Description
     */
    public function getPredicate()
    {
        return new Predicate();
    }

    /**
     *
     * @param string $keyFrom
     * @param string $keyTo
     * @param array $array
     * @return array
     */
    protected function mapField($keyFrom, $keyTo, array $array)
    {
        $array[$keyTo] = $array[$keyFrom];
        unset($array[$keyFrom]);
        return $array;
    }

    /**
     *
     * @param int $id
     * @param array $params
     * @return EntityInterface|null
     */
    public function fetch_old($id, $params = array())
    {
        $select = $this->getSlaveSql()
                ->select()
                ->where(array('id' => (int) $id));
        $resultset = $this->selectWith($select);
        if ($resultset->count()) {
            return $resultset->current();
        } else {
            // didn't get anything by that id
            return null;
        }
    }
    
    /**
     *
     * @param array $params
     * @return ResultsetInterface|null
     */
    public function fetchAll_old($params = array(), $condition = array())
    {
        $select = $this->getSlaveSql()->select();
        if (array_key_exists('fields', $params)) {
            $select->columns($params['fields']);
            if(!empty($condition)){
                $select = $select->where($condition);
            }
            $resultset = $this->selectWith($select);
            if ($resultset->count()) {
                return $resultset;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     *
     * @param array|EntityInterface $model
     * @return int
     */
    public function add($model)
    {
        if (is_array($model)) {
            if ((isset($model['create_time']) && empty($model['create_time'])) || ! isset($model['create_time'])) {
                $model['create_time'] = $this->getCreateTime();
            }
            
            $this->insert($model);
            return $this->lastInsertValue;
        } elseif (is_object($model)) {
            $data = $this->resolveDataInputFields($model);
            if (is_array($data) && count($data)) {
                if ((isset($data['create_time']) && empty($data['create_time'])) || ! isset($model['create_time'])) {
                    $data['create_time'] = $this->getCreateTime();
                }
                $affectedRows = $this->insert($data);
                if (method_exists($model, 'setId')) {
                    $model->setId($this->lastInsertValue);
                }
                // return $affectedRows;
                return $this->lastInsertValue;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public function remove()
    {
        
    }

    /**
     * Generate fields to be used in insert. Returns an
     * 
     * @return array|object
     */
    public function resolveDataInputFields($model)
    {
        return $model;
    }

    public function replace($id, $data)
    {
        try {
            if ($this->fetch($id)) {
                try {
                    return $this->update($data, array('id' => $id));
                } catch (\Exception $exc) {
                    throw $exc;
                }
            }
        } catch (\Exception $exc) {
            if ($exc instanceof \InvalidArgumentException) {
                throw new \InvalidArgumentException($exc->getMessage());
            } else {
                throw new \InvalidArgumentException('The patch failed');
            }
        }
    }

    /**
     * Get create time (now) Y-m-d H:i:s
     * 
     * @return string
     */
    public function getCreateTime()
    {
        $now = new \DateTime('now');
        return $now->format('Y-m-d H:i:s');
    }

    /**
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return \Helper\Mapper\TableGateway
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     *
     * @return Logger
     */
    public function getLogger()
    {
        if (!$this->logger) {
            $this->setLogger($this->serviceLocator->get('LoggerService'));
        }
        return $this->logger;
    }

    /**
     *
     * @param Logger $logger
     * @return \Helper\Mapper\TableGateway
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
        return $this;
    }

    public function beginTransaction()
    {
        $this->getAdapter()->getDriver()
                ->getConnection()->beginTransaction();
    }

    public function rollback()
    {
        $this->getAdapter()->getDriver()
                ->getConnection()->rollback();
    }

    public function commit()
    {
        $this->getAdapter()->getDriver()
                ->getConnection()->commit();
    }

    /**
     * @deprecated since version number
     * @param \Exception $ex
     * @return string
     */
    protected function getExceptionSummary(\Exception $ex)
    {
        return PHP_EOL .
                '>>>Exception' . ' - ' . $ex->getMessage() .
                PHP_EOL . '>>>Exception Code ' . $ex->getCode() .
                PHP_EOL . '>>>File ' . $ex->getFile() . ' Line ' . $ex->getLine();
    }

    /**
     *
     * @param \Exception $ex
     * @param string $file file the error occured in
     * @param string $line line in file where the error occured
     * @return string
     */
    protected function exceptionSummary(\Exception $ex, $file = null,
            $line = null)
    {
        return PHP_EOL .
                '>>>Exception' . ' - ' . $ex->getMessage() .
                PHP_EOL . '>>>Exception Code ' . $ex->getCode() .
                PHP_EOL . '>>>File ' . $ex->getFile() . ' Line ' . $ex->getLine() .
                PHP_EOL . '>>>Originating File ' . $file .
                PHP_EOL . '>>>Originating Line ' . $line;
    }

    /**
     *
     * @param ApiProblem $apiProblem
     * @return ApiProblemResponse
     */
    protected function apiProblemResponse(ApiProblem $apiProblem)
    {
        return new ApiProblemResponse($apiProblem);
    }

    /**
     *
     * @param int $statusCode
     * @param int $reasonPhrase
     * @return Response
     */
    protected function httpResponse($statusCode, $reasonPhrase)
    {
        $response = new Response();
        $response->setStatusCode($statusCode)
                ->setReasonPhrase($reasonPhrase);
        return $response;
    }

    /**
     * Delete soft i.e set is_deleted=1
     * 
     * @param int $itemId
     * @return boolean
     */
    public function deleteSoft($itemId)
    {
        $sql = new Sql($this->getAdapter());
        $update = $sql->update()
                ->table($this->getTable())
                ->where(array('id' => $itemId))
                ->set(array('is_deleted' => 1));
        $statement = $sql->prepareStatementForSqlObject($update);
        $result = $statement->execute();
        if ($result->getAffectedRows()) {
            return true;
        }
        return false;
    }
    
    /**
     * Delete permanently
     * 
     * @param int $itemId
     * @return boolean
     */
    public function delete($itemId)
    {
        $sql = new Sql($this->getAdapter());
        $delete = $sql->delete()
                ->from($this->getTable())
                ->where(array('id' => $itemId));
        $statement = $sql->prepareStatementForSqlObject($delete);
        $result = $statement->execute();
        if ($result->getAffectedRows()) {
            return true;
        }
        return false;
    }



    /**
     *
     * @param int $id
     * @param array $params
     * @param array $condition tables select conditions
     * @param bool $join Flag on whether to join with related tables or not. Defaults to FALSE
     * @param array $join_params Array containing: table to be joined ('table' key), joining condition ('on' key), 
     * fields to be pulled from the joined table ('fields' key), and the join type ('type' key)
     * @return EntityInterface|null
     */
    public function fetch($id, $params = array(), $condition = array(), $join = FALSE, $join_params = array())
    {
        $select = $this->getSlaveSql()->select();
        if (is_array($params) && array_key_exists('fields', $params)) {
            $select->columns($params['fields']);
        }
        
        if($join){
            foreach($join_params as $join_param){
                if(isset($join_param['table']) && isset($join_param['on'])){
                    $select->join(
                        $join_param['table'], 
                        $join_param['on'], 
                        isset($join_param['fields'])?$join_param['fields']:array(), 
                        isset($join_param['type'])?$join_param['type']:'inner' 
                    );
                }
            }
        }

        $fetch_condition = array($this->getTable() . '.id' => $id);
        if(is_array($condition) && !empty($condition)){
            $fetch_condition = array_merge($fetch_condition, $condition);
        }
        $select = $select->where($fetch_condition);
        $resultset = $this->selectWith($select);
        if ($resultset->count()) {
            return $resultset->current();
        }
        
        return null;
    }
    
    /**
     *
     * @param array $params
     * @param array $condition tables select conditions
     * @param bool $join Flag on whether to join with related tables or not. Defaults to FALSE
     * @param array $join_params Array containing: table to be joined ('table' key), joining condition ('on' key), 
     * fields to be pulled from the joined table ('fields' key), and the join type ('type' key)
     * @return ResultsetInterface|null
     */
    public function fetchAll($params = array(), $condition = array(),
            $join = FALSE, $join_params = array(), $order = null)
    {
        $select = $this->getSlaveSql()->select();
        if (is_array($params) && array_key_exists('fields', $params)) {
            $select->columns($params['fields']);
        }
            
        if($join){
            foreach($join_params as $join_param){
                if(isset($join_param['table']) && isset($join_param['on'])){
                    $select->join(
                        $join_param['table'], 
                        $join_param['on'], 
                        isset($join_param['fields'])?$join_param['fields']:array(), 
                        isset($join_param['type'])?strtolower($join_param['type']):'inner' 
                    );
                }
            }
        }
        
        if(is_array($condition) && !empty($condition)){
            $select = $select->where($condition);
        }
        if (!empty($order)) {
            $select = $select->order($order);
        }
        $resultset = $this->selectWith($select);
        if ($resultset->count()) {
            return $resultset;
        }
        return null;
    }
    
    /**
     * Update table
     * 
     * @param int $itemId
     * @return boolean
     */
    public function updateTable($condition, $values)
    {
        $sql = new Sql($this->getAdapter());
        $update = $sql->update()
                ->table($this->getTable())
                ->where($condition)
                ->set($values);
        $statement = $sql->prepareStatementForSqlObject($update);
        $result = $statement->execute();
        if ($result->getAffectedRows()) {
            return true;
        }
        return false;
    }
    

}

