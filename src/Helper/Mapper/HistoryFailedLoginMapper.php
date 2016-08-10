<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Mapper;

use Helper\Mapper\TableGateway;

/**
 * Description of HistoryFailedLoginMapper
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class HistoryFailedLoginMapper extends TableGateway
{

    /**
     * Set $this->table.attempts = 0
     *
     * @param null|int $userId
     */
    public function resetFailedAttempts($userId)
    {
        $affectedRows = null;
        try {
            if ($this->getFailedAttempts($userId) !== null) {
                $sql = $this->getSlaveSql();
                $update = $sql->update()
                        ->set(array('attempts' => 0))
                        ->where(array('user_id' => $userId));
                $result = $sql->prepareStatementForSqlObject($update)->execute();
                if ($result->count()) {
                    $affectedRows = $result->getAffectedRows();
                }
            }
        } catch (\Exception $exc) {
            $this->getLogger()->crit($exc->getTraceAsString());
        }
        return $affectedRows;
    }

    /**
     *
     * @param int $userId
     * @return int|null
     */
    public function getFailedAttempts($userId)
    {
        $failedAttempts = null;
        $sql = $this->getSlaveSql();
        $select = $sql->select()->columns(array('attempts'))
                ->where(array('user_id' => $userId))
                ->limit(1);
        $result = $sql->prepareStatementForSqlObject($select)->execute();
        if ($result->count()) {
            $failedAttempts = (int) $result->current()['attempts'];
        }
        return $failedAttempts;
    }

}
