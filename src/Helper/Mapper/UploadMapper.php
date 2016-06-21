<?php

namespace Helper\Mapper;

use Helper\Mapper\TableGateway;
use Zend\Filter\BaseName;

/**
 * Description of UploadMapper
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class UploadMapper extends TableGateway
{

    const UPLOAD_PATH = './data/uploads';

    /**
     *
     * @param int $userId
     * @param array $uploadData
     */
    public function save($userId, array $uploadData)
    {
        $filter = new BaseName();
        foreach ($uploadData as $category => $colData) {
            if (is_array($colData)) {
                // remove an unwanted key
                if (array_key_exists('error', $colData)) {
                    unset($colData['error']);
                }
                // avoid saving full path to files
                $colData['tmp_name'] = $filter->filter($colData['tmp_name']);

                // mark files as stale if they exist
                if ($this->fileExists($userId, $category)) {
                    $this->update(array('current' => 0),
                            array('user_id' => $userId
                        , 'category' => $category));
                }
                // run a fresh insert
                $colData['category'] = $category;
                $colData['user_id'] = $userId;
                $this->insert($colData);
            }
        }
    }

    /**
     * Assert if a file exists
     *
     * @param int $userId
     * @param string $category
     * @return boolean
     */
    public function fileExists($userId, $category)
    {
        $select = $this->getSlaveSql()
                ->select()
                ->columns(array('id'))
                ->where(array('user_id' => (int) $userId, 'category' => $category));
        $resultSet = $this->selectWith($select);
        if ($resultSet->count()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns type, name and size of a file
     *
     * @param int $userId
     * @param array $params
     * @return boolean|\Client\Entity\Upload
     * @throws \InvalidArgumentException
     */
    public function fetchFile($userId, array $params)
    {

        if (array_key_exists('tmp_name', $params)) {
            $select = $this->getSlaveSql()
                    ->select()
                    ->columns(array('type', 'name', 'size', 'tmp_name'))
                    ->where(array(
                // ensure the file belongs to the user
                'user_id' => (int) $userId,
                'tmp_name' => $params['tmp_name'],
                // select the latest upload
                'current' => 1,
            ));
            $resultSet = $this->selectWith($select);
            if ($resultSet->count()) {
                // we have the correct file
                return $resultSet->current();
            } else {
                return false;
            }
        } else {
            throw new \InvalidArgumentException('Functionality does not currently exist');
        }
    }

    /**
     * Only current data is fetched
     *
     * @param int $userId
     * @param array $columns
     * @return null|\Zend\Db\ResultSet\HydratingResultSet
     */
    public function fetchAllByUserId($userId,
            array $columns = array('id', 'name', 'type', 'tmp_name'))
    {
        $select = $this->getSlaveSql()->select()
                ->columns($columns)
                ->where(array(
            'user_id' => $userId,
            'current' => 1,
        ));
        $resultset = $this->selectWith($select);
        if ($resultset->count()) {
            return $resultset;
        } else {
            return null;
        }
    }

}