<?php
namespace ORC\DBAL\Common;
use \ORC\DAO\Dao;
use \ORC\Util\Callback;
class Update extends Common {
	protected $_update_fields = array();
	protected $_increament_fields = array();
	/**
	 * 
	 * @return bool
	 */
	public function execute() {
		Callback::get('dbal.update.pre_execute')->call($this);
		$dao = $this->getDao();
		if ($this->_has_empty_in) {
		    $result = false;
		} else {
    		$this->prepare($dao);
    		$result = $this->executeDao($dao);
		}
		Callback::get('dbal.update.post_execute')->call($this, $dao, $result);
		return $result;
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
		$this->_update_fields[$key] = $value;
		return $this;
	}
	
	public function increase($key, $value) {
	    $fields = $this->_table->getSchema()->get('fields');
	    if(!isset($fields[$key])) {
	        throw new \ORC\DAO\Exception\Exception('Unknown field');
	    }
	    if ($value == 0) return;
	    if ($value > 0) {
	        $op = '+';
	    } else {
	        $op = '-';
	    }
	    $this->_increament_fields[$key] = array('op' => $op, 'value' => abs($value));
	    return $this;
	}
	
	public function decrease($key, $value) {
	    return $this->increase($key, 0 - $value);
	}
	protected function executeDao(Dao $dao) {
		$values = array();
		//first update value
		foreach ($this->_update_fields as $value) {
			$values[] = $value;
		}
		foreach ($this->_increament_fields as $field_name => $options) {
		    $values[] = $options['value'];
		}
		//then where
		$values = array_merge($values, $this->getWhereValues());
		return $dao->execute($values);
	}
	
	/* (non-PHPdoc)
	 * @see \ORC\DBAL\Common\Common::buildStatement()
	 */
	protected function buildStatement() {
		if (count($this->_update_fields) == 0 && count($this->_increament_fields) == 0) {
			throw new \ORC\DAO\Exception\Exception('Nothing changed');
		}
		$sql = 'UPDATE ' . $this->_table->getTableName() . ' SET ';
		$updates = array();
		foreach ($this->_update_fields as $field_name => $value) {
			$updates[] = sprintf('%s = ?', $field_name);
		}
		foreach ($this->_increament_fields as $field_name => $options) {
		    $updates[] = sprintf('%s = %s %s ?', $field_name, $field_name, $options['op']);
		}
		$sql .= implode(', ', $updates);
		$sql .= $this->buildWhere();
		return $sql;
	}
}