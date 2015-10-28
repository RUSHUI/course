<?php
namespace ORC\Util;
use ORC\DAO\Table\DataList;
use ORC\Exception\SystemException;
use ORC\Util\Pagination\IRender;
use ORC\Exception\TemplateException;
class Pagination {
    protected $datalist;
    protected $totalCount;
    protected $page;
    protected $pageSize;
    public function __construct(DataList $datalist = null, $totalCount = 0, $page = 1, $pageSize = 20) {
        $this->setDatalist($datalist);
        $this->setTotalCount($totalCount);
        $this->setPage($page);
        $this->setPageSize($pageSize);
    }
    
    public function getTotalPage() {
        return ceil($this->totalCount/$this->pageSize);
    }
    
    public function toHTML($action_name, array $params = array(), $renderer = null) {
        if ($renderer == null) {
            $render = new \ORC\Util\Pagination\DefaultRender($action_name, $params);
        } else {
            $render = new $renderer($action_name, $params);
            if (!($render instanceof IRender)) {
                throw new TemplateException('Wrong Renderer used for pagination', $renderer);
            }
        }
        return $render->toHtml($this);
    }

	/**
     * @return the $datalist
     */
    public function getDatalist()
    {
        return $this->datalist;
    }

	/**
     * @return the $totalCount
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

	/**
     * @return the $page
     */
    public function getPage()
    {
        return $this->page;
    }

	/**
     * @return the $pageSize
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

	/**
     * @param DataList $datalist
     */
    public function setDatalist($datalist)
    {
        $this->datalist = $datalist;
    }

	/**
     * @param field_type $totalCount
     */
    public function setTotalCount($totalCount)
    {
        $this->totalCount = $totalCount;
    }

	/**
     * @param field_type $page
     */
    public function setPage($page)
    {
        $page = (int) $page;
        if ($page < 1) {
            $page = 1;
        }
        $this->page = $page;
    }

	/**
     * @param field_type $pageSize
     */
    public function setPageSize($pageSize)
    {
        $pageSize = (int) $pageSize;
        if ($pageSize < 1) {
            throw new SystemException('Invalid Page Size', $pageSize);
        }
        $this->pageSize = $pageSize;
    }

    
    
    
}