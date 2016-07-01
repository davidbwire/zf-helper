<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Util;

use Helper\Util\TokenGenerator;
use Zend\Crypt\Password\Bcrypt;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use Zend\Log\Logger;

/**
 * Description of PasswordManager
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class PasswordManager
{

    /**
     *
     * @var TokenGenerator
     */
    protected $tokenGenerator;

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

    public function __construct(Adapter $dbAdapter, Logger $logger = null)
    {
        $this->tokenGenerator = new TokenGenerator($logger);
        $this->dbAdapter = $dbAdapter;
        $this->logger = $logger;
    }

    /**
     * Use token generator to generate a plain text string
     * 
     * @param int $length
     * @return string
     */
    public function generatePlainTextPassword($length = 10)
    {
        $aPlainTextPassword = str_split($this->tokenGenerator->generateToken(),
                $length);
        return array_shift($aPlainTextPassword);
    }

    /**
     *
     * @param int $userId
     * @param string $plainTextPassword
     * @return boolean
     */
    public function updatePassword($userId, $plainTextPassword)
    {
        try {
            $bcrypt = new Bcrypt();
            $bcrypt->setCost(14);
            $encryptedPassword = $bcrypt->create($plainTextPassword);
            $sql = new Sql($this->dbAdapter, 'user');
            $update = $sql->update()
                    ->set(['password' => $encryptedPassword])
                    ->where(['id' => $userId]);
            $result = $sql->prepareStatementForSqlObject($update)
                    ->execute();
            if ($result->getAffectedRows()) {
                return true;
            }
        } catch (\Exception $exc) {
            if ($this->logger instanceof Logger) {
                $this->logger
                        ->crit($exc->getMessage()
                                . ' File - ' . __FILE__ . ' Line - ' . __LINE__);
            }
        }
        return false;
    }

}
