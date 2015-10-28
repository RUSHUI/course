<?php
namespace ORC\DBAL\Common;
use ORC\DAO\Table;
use ORC\DAO\DaoFactory;
use ORC\DAO\Dao;
use ORC\Util\Logger;
use ORC\Exception\SystemException;
use ORC\DAO\Util;
abstract class Common {
	/**
	 *
	 * @var \ORC\DAO\Table
	 */
	protected $_table;
	protected $_dao;
	protected $_statement;
	protected $_datarow_class_name;
	protected $_where = array();
	
	protected $_has_empty_in = false;
	
	/**
	 * @return Ambigous <\ORC\DAO\Dao, false>
	 */
	//abstract public function execute();
	
	public function __construct(Table $table) {
		$this->_table = $table;
	}
	
	/**
	 *
	 * @param string $key
	 * @return boolean
	 */
	protected function isPK($key) {
		return strcasecmp($key, $this->_table->getPrimaryKey()) == 0;
	}
	
	/**
	 *
	 * @return \ORC\DAO\Dao
	 */
	public function getDao() {
		if (!isset($this->_dao)) {
		    $server_name = $this->_table->getServerName();
			$this->_dao = DaoFactory::get($server_name);
		}
		return $this->_dao;
	}
	
	public function setDao(Dao $dao) {
	    $this->_dao = $dao;
	}
	
	public function beginTransaction() {
	    return $this->getDao()->beginTransaction();
	}
	
	public function inTransaction() {
	    return $this->getDao()->inTransaction();
	}
	
	public function commit() {
	    return $this->getDao()->commit();
	}
	
	public function rollback() {
	    return $this->getDao()->rollBack();
	}
	
	public function pk($value) {
	    $pk = explode(',', $this->_table->getPrimaryKey());
	    if (count($pk) == 1) {
	        $this->findBy($pk[0], array($value));
	    } else {
	        throw new SystemException('not support mult primary keys');
	    }
	}
	/**
	 * 
	 * @param unknown $name
	 * @param array $args
	 * @return \ORC\DBAL\Common\Common
	 */
	public function __call($name , array $args) {
		$name = strtolower($name);
		if (substr($name, 0, 2) == 'by') {
			$key = substr($name, 2);
			return $this->findBy($key, $args);
		} elseif (substr($name, 0, 5) == 'andby') {
			$key = substr($name, 5);
			return $this->appendFindBy($key, $args);
		} elseif (substr($name, 0, 4) == 'like') {
		    $key = substr($name, 4);
		    return $this->likeBy($key, $args);
		} elseif (substr($name, 0, 8) == 'leftlike') {
		    $key = substr($name, 8);
		    return $this->likeBy($key, $args, 'left');
		} elseif (substr($name, 0, 9) == 'rightlike') {
		    $key = substr($name, 9);
		    return $this->likeBy($key, $args, 'right');
		} elseif (substr($name, 0, 4) == 'opby') {
		    $key = substr($name, 4);
		    return $this->opBy($key, $args);
		}
		throw new SystemException('Unknown method ' . $name);
	}
	
	public function findBy($key, array $args) {
		$argc = count($args);
		//to support either call by findxxx($id1, $id2) or findxxxx(array($id1, $id2))
		if ($argc == 1) {
			$args = array_shift($args);
			if (is_array($args)) {
				$argc = count($args);
			} else {
				$args = array($args);
			}
		}
		//find the match key
		$key = $this->getFieldName($key);
		if ($argc == 1) {
			$where = sprintf('%s = ?', $key);
			$value = array_shift($args);
		} else {
		    //if $args is empty, this will cause a sql error
		    if (count($args) == 0) {
		        //execute will return empty record
		        $this->_has_empty_in = true;
		        return $this;
		    }
			$where = sprintf('%s in (%s)', $key, implode(',', array_fill(0, $argc, '?')));
			$value = $args;
		}
		$this->_where[] = array('where' => $where, 'value' => $value);
		return $this;
	}
	
	public function appendFindBy($key, array $args) {
		return $this->findBy($key, $args);
	}
	
	public function likeBy($key, array $args, $side = null) {
	    $argc = count($args);
	    //to support either call by findxxx($id1, $id2) or findxxxx(array($id1, $id2))
	    if ($argc == 1) {
	        $args = array_shift($args);
	        if (is_array($args)) {
	            $argc = count($args);
	        } else {
	            $args = array($args);
	        }
	    }
	    //find the match key
	    $key = $this->getFieldName($key);
	    if ($argc == 1) {
	        $where = sprintf('%s LIKE ?', $key);
	        if ($side == 'left') {
	            $value = '%' . array_shift($args);
	        } elseif ($side == 'right') {
	            $value = array_shift($args) . '%';
	        } else {
	           $value = '%' . array_shift($args) . '%';
	        }
	    } else {
	        throw new SystemException('like does not support mulitiple values');
	    }
	    $this->_where[] = array('where' => $where, 'value' => $value);
	    return $this;
	}
	
	public function opBy($key, array $args) {
	    $key = $this->getFieldName($key);
	    $where = sprintf('%s %s ?', $key, $args[0]);
	    $this->_where[] = array('where' => $where, 'value' => $args[1]);
	    return $this;
	}
	protected function getFieldName($key) {
		return $this->_table->getFilterName($key);
	}
	/**
	 *
	 * @param string $class_name
	 * @return boolean
	 */
	protected function checkValidDataRowClass($class_name) {
		return \ORC\DAO\Table\Util::checkValidDataRowClass($class_name);
	}
	
	protected function prepare(Dao $dao) {
		$sql = $this->buildStatement();
		Logger::getInstance('DBAL')->addInfo("prepared sql:$sql");
		return $dao->prepare($sql);
	}
	
	abstract protected function buildStatement();
	
	protected function buildWhere() {
		//@todo add or support
		$wheres = array();
		foreach ($this->_where as $where) {
			if (empty($where['where'])) continue;
			$wheres[] = $where['where'];
		}
		if (count($wheres)) {
			return ' WHERE (' . implode(' AND ', $wheres) . ')';
		} else {
			return '';
		}
	}
	
	protected function getWhereValues() {
		$values = array();
		foreach ($this->_where as $where) {
			if (empty($where['where'])) continue;
			if (is_array($where['value'])) {
				foreach ($where['value'] as $v) {
					$values[] = $v;
				}
			} else {
				$values[] = $where['value'];
			}
		}
		return $values;
	}
}