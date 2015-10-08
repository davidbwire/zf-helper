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
    public function fetch($id, $params = array())
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
    public function fetchAll($params = array())
    {
        $select = $this->getSlaveSql()->select();
        if (array_key_exists('fields', $params)) {
            $select->columns($params['fields']);
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
            if (isset($model['create_time']) && empty($model['create_time'])) {
                $model['create_time'] = $this->getCreateTime();
            }
            return $this->insert($model);
        } elseif (is_object($model)) {
            $data = $this->resolveDataInputFields($model);
            if (is_array($data) && count($data)) {
                if (isset($data['create_time']) && empty($data['create_time'])) {
                    $data['create_time'] = $this->getCreateTime();
                }
                $affectedRows = $this->insert($data);
                if (method_exists($model, 'setId')) {
                    $model->setId($this->lastInsertValue);
                }
                return $affectedRows;
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
    protected function getCreateTime()
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

}
