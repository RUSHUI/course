<?php
namespace ORC\DBAL\Common;
use ORC\DAO\Dao;
use ORC\Util\Callback;
use ORC\Util\Observer;
abstract class Select extends Common {
	const DEFAULT_PAGE_SIZE = 20;
	protected $_limit;
	protected $_order_by;
	protected $_group_by;
	protected $_total_count;
	protected $_select;
	
	/**
	 * @return \ORC\DAO\Table\DataList
	 */
	public function execute() {
		Callback::get('dbal.select.pre_execute')->call($this);
		Observer::getObserver('dbal.select.pre_execute')->notify();
		$dao = $this->getDao();
		if ($this->_has_empty_in) {
		    $data = array();
		    $this->_total_count = 0;
		    //no values found
		} else {
    		$this->prepare($dao);
    		$this->executeDao($dao);
    		$this->setFetchMode($dao);
    		$data = $dao->fetchAll(null);
    		if (!empty($this->_limit)) {
    			$this->_total_count = null;
    		} else {
    			$this->_total_count = count($data);
    		}
		}
		$data = new \ORC\DAO\Table\DataList($data, $this->_table);
		Callback::get('dbal.select.post_execute')->call($this, $dao, $data);
		Observer::getObserver('dbal.select.post_execute')->notify();
		return $data;
	}

	/**
	 * used to run sql temporary
	 * should try to use execute instead
	 * @param string $sql
	 * @return \ORC\DAO\Table\DataList
	 */
	public function query($sql) {
	    $dao = $this->getDao();
	    $dao->query($sql);
	    $this->setFetchMode($dao);
	    $data = $dao->fetchAll(null);
	    $this->_total_count = count($data);
	    $data = new \ORC\DAO\Table\DataList($data, $this->_table);
	    return $data;
	}
	/**
	 * get only one row of the data
 	 * @return Ambigous <\ORC\DAO\Table\DataRow, false>
	 */
	public function getOne() {
		$this->limit(array(0, 1));
		$data = $this->execute();
		if ($data instanceof \ORC\DAO\Table\DataList) {
			return $data->current();
		}
		return false;
	}
	/**
	 * 
	 * @param int $page
	 * @param string $pagesize
	 * @return \ORC\DBAL\Common\Select
	 */
	public function setPage($page, $pagesize = null) {
		return $this->limit(array($page, $pagesize));
	}
	
	/**
	 * @return int the total count without page
	 */
	abstract public function getTotalCount();
	/**
	 * Only useful when use select
	 * @param string $class_name
	 * @return \ORC\DBAL\Common\Select
	 */
	public function setDataRowClass($class_name) {
	    if (is_object($class_name)) {
	        $class_name = get_class($class_name);
	    }
		$this->checkValidDataRowClass($class_name);
		$this->_datarow_class_name = $class_name;
		return $this;
	}

	/**
	 * 
	 * @param string $order_by
	 * @return \ORC\DBAL\Common\Select
	 */
	public function orderBy($order_by) {
		$this->_order_by = $order_by;
		return $this;
	}
	
	/**
	 * 
	 * @param string $group_by
	 * @return \ORC\DBAL\Common\Select
	 */
	public function groupBy($group_by) {
		$this->_group_by = $group_by;
		return $this;
	}
	
	/**
	 * 
	 * @param string $name
	 * @param array $args
	 * @return \ORC\DBAL\Common\Select
	 */
	public function __call($name , array $args) {
		$name = strtolower($name);
		if ($name == 'limit') {
			return $this->limit($args);
		}
		return parent::__call($name, $args);
	}
	
	public function setSelect($select) {
	    $this->_select = $select;
	}
	/**
	 * 
	 * @param array $args
	 * @return \ORC\DBAL\Common\Select
	 */
	abstract protected function limit(array $args);
	
	protected function buildStatement() {
		$sql = 'SELECT ' . $this->getSelect() . ' FROM ' . $this->_table->getTableName() . $this->buildWhere();
		$sql .= $this->buildGroupBy();
		$sql .= $this->buildOrderBy();
		return $sql;
	}
	
	protected function buildOrderBy() {
		if (!empty($this->_order_by)) {
			return sprintf(' ORDER BY %s', $this->_order_by);
		}
		return '';
	}
	
	protected function buildGroupBy() {
		if (!empty($this->_group_by)) {
			return sprintf(' GROUP BY %s', $this->_group_by);
		}
		return '';
	}
	protected function executeDao(Dao $dao) {
		$values = $this->getWhereValues();
		return $dao->execute($values);
	}
	
	protected function setFetchMode(Dao $dao) {
		if(!isset($this->_datarow_class_name)) {
			$this->_datarow_class_name = '\ORC\DAO\Table\DataRow';
		}
		return $dao->setFetchMode(DAO::FETCH_CLASS | DAO::FETCH_PROPS_LATE, $this->_datarow_class_name);
	}
	
	protected function getSelect() {
	    if (!$this->_select) {
	        $this->_select = '*';
	    }
	    return $this->_select;
	}
}