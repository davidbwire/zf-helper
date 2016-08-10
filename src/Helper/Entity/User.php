<?php

namespace Helper\Entity;

use ZfcUser\Entity\UserInterface;
use ZfcUser\Entity\User as ZfcUserEntity;

class User extends ZfcUserEntity
{

    /**
     * @var string
     */
    protected $id;

    /**
     * Get id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id.
     *
     * @param string $id
     * @return UserInterface
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

}
