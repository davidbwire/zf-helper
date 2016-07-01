<?php

namespace Helper\Entity;

/**
 * Description of Base
 *
 * This is a base class extended by all entities
 *
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class Base implements EntityInterface
{

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string Description
     */
    protected $createTime;

    /**
     * @var string Description
     */
    protected $updateTime;

    /**
     *
     * @param int $id
     * @return \Helper\Entity\Base
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int Description
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param string $createTime
     * @return \Helper\Entity\Base
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;
        return $this;
    }

    /**
     * @return string Description
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     *
     * @param string $updateTime
     * @return \Helper\Entity\Base
     */
    public function setUpdateTime($updateTime)
    {
        $this->updateTime = $updateTime;
        return $this;
    }

    /**
     * @return string Description
     */
    public function getUpdateTime()
    {
        return $this->updateTime;
    }

}
