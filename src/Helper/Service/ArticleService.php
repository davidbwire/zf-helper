<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Service;

use Zend\Navigation\Navigation;
use Zend\Http\Request;
use Helper\Mapper\ArticleMapper;

/**
 * Used to generate and save pages
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class ArticleService
{

    /**
     *
     * @var \Zend\Navigation\Navigation
     */
    protected $navigation;

    /**
     *
     * @var \Zend\Http\Request
     */
    protected $request;

    /**
     *
     * @var \Helper\Mapper\ArticleMapper
     */
    protected $pageMapper;

    /**
     * Get pages.
     *
     * @return array|null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getPages()
    {
        $result = $this->getPageMapper()->fetchAll();
        return $result;
    }

    /**
     * Save page
     * 
     * @param type $entity
     */
    public function save($entity)
    {

    }
    /**
     * Delete page - Looks through post data before removal
     * 
     * @param type $data
     */
    public function remove()
    {

    }

    /**
     * Get page by URL String.
     *
     * @param $urlString
     * @return bool|mixed
     */
    public function getPageByUrlString($urlString)
    {
        if (!$this->getNavigation()->findOneBy('url_string', $urlString)) {
            return false;
        }
        $result = $this->getPageMapper()->fetchByUrlString(($urlString ? $urlString : ''));
        return $result;
    }

    /**
     *
     * @param \Zend\Navigation\Navigation $navigation
     * @return \Helper\Service\ArticleService
     */
    public function setNavigation(Navigation $navigation)
    {
        $this->navigation = $navigation;
        return $this;
    }

    /**
     *
     * @return \Zend\Navigation\Navigation
     */
    public function getNavigation()
    {
        return $this->navigation;
    }

    /**
     *
     * @param \Helper\Mapper\ArticleMapper $pageMapper
     * @return \Helper\Service\ArticleService
     */
    public function setPageMapper(ArticleMapper $pageMapper)
    {
        $this->contentMapper = $pageMapper;
        return $this;
    }

    /**
     *
     * @return \Helper\Mapper\ArticleMapper
     */
    public function getPageMapper()
    {
        return $this->pageMapper;
    }

    /**
     *
     * @param \Zend\Http\Request $request
     * @return \Helper\Service\ArticleService
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     *
     * @return \Zend\Http\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

}
