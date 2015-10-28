<?php
namespace ORC\DBAL\Common;
use ORC\DAO\Dao;
use ORC\Util\Callback;
use ORC\Util\Observer;
abstract class Insert extends Common {
	protected $_data = array();
	protected $_duplicates = array();
	protected $_last_id;
	protected $_ignore = false;
	protected $_raw_duplicate;
	/**
	 * 
	 * @return int/string the last insert id
	 */
	public function execute() {
		Callback::get('dbal.insert.pre_execute')->call($this);
		Observer::getObserver('dbal.insert.pre_execute')->notify();
		$dao = $this->getDao();
		if ($this->_has_empty_in) {
		    return false;
		} else {
    		$this->prepare($dao);
    		if (!$this->executeDao($dao)) {
    		    return false;
    		}
    		$this->_last_id = $dao->lastInsertId();
		}
		Callback::get('dbal.insert.post_execute')->call($this, $dao, $this->_last_id);
		Observer::getObserver('dbal.insert.post_execute')->notify();
		return $this->_last_id;
	}
	
	public function getLastInsertId() {
		return $this->_last_id;
	}
	
	public function ignore($ignore = null) {
	    if ($ignore === null) {
	        return $this->_ignore;
	    }
	    $this->_ignore = (bool) $ignore;
	}
	
	public function setDuplicate($duplicates) {
		if (is_array($duplicates)) {
			foreach ($duplicates as $duplicate) {
				$this->setDuplicate($duplicate);
			}
			return $this;
		}
		if (array_search($duplicates, $this->_duplicates) === false) {
			$fields = $this->_table->getSchema()->get('fields');
			if (!isset($fields[$duplicates])) {
				throw new \ORC\DAO\Exception\Exception('Unknown field');
			}
			$this->_duplicates[] = $duplicates;
		}
		return $this;
	}
	
	public function setRawDuplicate($duplicate) {
	    $this->_raw_duplicate = $duplicate;
	}
	
	public function set($key, $value = null) {
		if (is_array($key)) {
			foreach ($key as $k => $v) {
				$this->set($k, $v);
			}
			return $this;
		}
		$fields = $this->_table->getSchema()->get('fields');
		if(!isset($fields[$key])) {
			throw new \ORC\DAO\Exception\Exception('Unknown field');
		}
		$this->_data[$key] = $value;
		return $this;
	}
	
	public function __call($name , array $args) {
	    $name = strtolower($name);
	    if (substr($name, 0, 3) == 'set') {
	        $key = substr($name, 3);
	        return $this->setBy($key, $args);
	    } else {
	        parent::__call($name, $args);
	    }
	}
	protected function setBy($key, array $args) {
	    $key = $this->getFieldName($key);
	    return $this->set($key, array_shift($args));
	}
	
	/* (non-PHPdoc)
	 * @see \ORC\DBAL\Common\Common::buildStatement()
	 */
	protected function buildStatement() {
		if (count($this->_data) == 0) {
			throw new \ORC\DAO\Exception\Exception('Nothing changed');
		}
		$sql = sprintf('INSERT %sINTO %s (%s) VALUES (%s)', ($this->_ignore ? 'IGNORE ' : ''), $this->_table->getTableName(), implode(', ', array_keys($this->_data)), implode(',', array_fill(0, count($this->_data), '?')));
		if (count($this->_duplicates) || $this->_raw_duplicate) {
			$sql .= $this->getDuplicateSQL();
		}
		return $sql;
	}
	
	/**
	 * for duplicate
	 * like mysql ON DUPLICATE KEY UPDATE
	 * @return string the SQL fragment
	 */
	abstract protected function getDuplicateSQL();
	
	protected function executeDao(Dao $dao) {
		$values = array();
		//first update value
		foreach ($this->_data as $value) {
			$values[] = $value;
		}
		return $dao->execute($values);
	}

	public function findBy($key, array $args) {
		throw new \ORC\Exception\Exception('Insert can not use by statement');
	}
}