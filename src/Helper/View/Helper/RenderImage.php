<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use Exception;
use Zend\Log\Logger;

/**
 * Given a table name and row name this helper generates a profile picture
 * link
 *
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class RenderImage extends AbstractHelper
{

    /**
     *
     * @var Adapter
     */
    protected $dbAdapter;

    /**
     *
     * @var Logger
     */
    protected $logger;

    public function __construct(Adapter $dbAdapter, Logger $logger)
    {
        $this->dbAdapter = $dbAdapter;
        $this->logger = $logger;
    }

    /**
     *
     * @param string $tableName The table holding the image reference
     * @param string $rowName The specific column to pull the image reference
     * @param array $where ['id' => $id] Unique identifier for a specific row
     * @param string $imagePath The image path (it's always appended to the db result
     *  but can)
     * @return type
     */
    public function __invoke($tableName = null, $rowName = null,
            array $where = [], $imagePath = null)
    {
        $imageTemplate = '<img src="%s">';

        /*
         * @todo finalize check later
         * if ($imagePath !== null) {
          // check if path is valid and log if otherwise
          // Greedy ^\/([a-zA-Z]|-|_)+?(\/([a-z]|[A-Z]|-|_)+?)*?\.(?:png|gif|jpeg|jpg)$
          // Non-Greedy ^\/(?:[a-zA-Z]|-|_)+?(?:\/(?:[a-z]|[A-Z]|-|_)+?)*?\.(?:png|gif|jpeg|jpg)$
          if (preg_match('/^\/(?:[a-zA-Z]|-|_)+?(?:\/(?:[a-z]|[A-Z]|-|_)+?)*?\.(?:png|gif|jpeg|jpg)$/',
          $imagePath) !== 1) {
          $this->logger->crit('The path provided is invalid ' . __METHOD__);
          }
          }
         */
        $serverRoot = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT');
        // check if user passed a single param that is an image path
        if ((func_num_args() === 1) && is_readable($serverRoot . func_get_arg(0))) {
            return sprintf($imageTemplate, func_get_arg(0));
        }
        // check if the user passed the image path as a last param
        if (empty($tableName) || empty($rowName) || empty($where)) {
            if (is_readable($serverRoot . $imagePath)) {
                return sprintf($imageTemplate, $imagePath);
            } else {
                $this->logger->err($serverRoot . $imagePath . 'is not readable');
            }
        }
        // try retreiving the image from db.
        if (!empty($tableName) && !empty($rowName) && !empty($where)) {
            $result = $this->fetchImage($tableName, $rowName, $where);
            if (!empty($result)) {
                // append the image path
                return sprintf($imageTemplate, $imagePath . $result);
            } else {
                $this->logger->err('Image path could not be retreived from database.');
            }
        }
        // return an empty image
        return sprintf($imageTemplate, $imagePath);
    }

    /**
     *
     * @param string $tableName
     * @param string $rowName
     * @param array $where
     * @return string|null
     */
    private function fetchImage($tableName, $rowName, array $where)
    {
        try {
            $sql = new Sql($this->dbAdapter);
            $select = $sql->select()
                    ->from($tableName)
                    ->columns(array($rowName))
                    ->where($where)
                    ->limit(1);
            $result = $sql
                    ->prepareStatementForSqlObject($select)
                    ->execute();
            if (!$result->count()) {
                return null;
            }
            return $result->current()[$rowName];
        } catch (Exception $exc) {
            $this->logger->err($exc->getMessage());
            return null;
        }
    }

}
