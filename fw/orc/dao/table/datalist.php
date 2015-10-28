<?php
namespace ORC\DAO\Table;
use ORC\DAO\Table;
use ORC\DAO\Exception\Exception;
use ORC\Exception\SystemException;
use ORC\Util\Pagination;
class DataList implements \Iterator, \Countable {
	protected $_data;
	protected $_index = 0;
	protected $_table;
	public function __construct(Array $data, Table $table) {
		foreach ($data as $v) {
			if ($v instanceof DataRow) {
				$v->setTable($table);
			} else {
			    throw new Exception('Wrong input data type');
			}
		}
		$this->_data = array_values($data);//reset the index
		$this->_table = $table;
	}
	
	/**
	 * append a datarow object to the list
	 * @param DataRow $data
	 * @return boolean false if data table not match
	 */
	public function append(DataRow $data) {
	    if ($data->getTable()) {
	        if ($data->getTable()->getTableName() != $this->_table->getTableName()) {
	            return false;
	        }
	    } else {
	        $data->setTable($this->_table);
	    }
	    $this->_data[] = $data;
	    return true;
	}
	
	/**
	 * 
	 * @return Table
	 */
	public function getTable() {
	    return $this->_table;
	}
	/**
	 * return an array include all the values for name $name
	 * @param string $name
	 * @return array
	 */
	public function getByName($name) {
	    $result = array();
	    foreach ($this->_data as $row) {
	        $result[] = $row[$name];
	    }
	    return $result;
	}
	
	/**
	 * get an array
	 * @param string $key the key name of the array
	 * @param string $value the value name from data to give to array, if value is null, will use the whole row as value
	 * @return \ORC\DAO\Table\DataRow[]
	 */
	public function toArray($key, $value = null) {
	    $result = array();
	    foreach ($this->_data as $row) {
	        $result[$row[$key]] = $value ? $row[$value] : $row;
	    }
	    return $result;
	}
	
	/**
	 * Group the values by $key
	 * @param string $key
	 * @param string $value
	 * @return \ORC\DAO\Table\DataRow[]
	 */
	public function groupBy($key, $value = null) {
	    $result = array();
	    foreach ($this->_data as $row) {
	        if (!isset($result[$row[$key]])) {
	            $result[$row[$key]] = array();
	        }
	        $result[$row[$key]][] = $value ? $row[$value] : $row;
	    }
	    return $result;
	}
	/**
	 * convert current datalist to UIDataTable for display
	 * @param callable $function callback function for each row
	 * @return \ORC\UI\Data\Table
	 */
	public function toUIDataTable(callable $callback = null, callable $row_callback = null) {
	    $table = new \ORC\UI\Data\Table();
	    if (!empty($callback)) {
	        $table->setCallBack($callback);
	    }
	    if (!empty($row_callback)) {
	        $table->setRowCallBack($callback);
	    }
	    $table->loadData($this);
	    return $table;
	}
	
	/**
	 * implement \Countable::count() so you can use count($this) directory
	 * @return number
	 */
	public function count() {
		return count($this->_data);
	}
	
	public function toPagination($page, $pageSize) {
	    $page = (int)$page;
	    $pageSize = (int)$pageSize;
	    if ($page < 1) {
	        $page = 1;
	    }
	    $data = array_slice($this->_data, ($page -1) * $pageSize, $pageSize);
	    return new Pagination(new self($data, $this->_table), $this->count(), $page, $pageSize);
	}
	
	/**
	 * 找到所有$name值为$value的row
	 * @param string $name
	 * @param mixed $value
	 * @return \ORC\DAO\Table\DataRow[]
	 */
	public function find($name, $value) {
	    $result = array();
	    foreach ($this->_data as $row) {
	        if ($row[$name] == $value) {
	            $result[] = $row;
	        }
	    }
	    return $result;
	}
	
	/**
	 * 试图找到一个$name值为$value的row
	 * @param string $name
	 * @param mixed $value
	 * @return \ORC\DAO\Table\DataRow
	 */
	public function findOne($name, $value) {
	    foreach ($this->_data as $row) {
	        if ($row[$name] == $value) {
	            return $row;
	        }
	    }
	    return false;
	}
	
	public function __call($name, $args) {
	    $name = strtolower($name);
	    if (substr($name, 0, 5) == 'getby') {
	        $key = substr($name, 5);
    	    $argc = count($args);
    		//to support either call by getbyxxx($id1, $id2) or getbyxxxx(array($id1, $id2))
    		if ($argc == 1) {
    			$args = array_shift($args);
    			if (is_array($args)) {
    				$argc = count($args);
    			} else {
    				$args = array($args);
    			}
    		}
    		return $this->getBy($key, $args);
	    }
	    throw new SystemException('Unknown method ' . $name);
	}
	
	/**
	 * sort the datarows
	 * @param string $field_name the field name for sort
	 * @param bool $asc true to sort ascending, false to sort decending 
	 * @param bool $convert_charset for sort by Chinese characters, set this value to true
	 * @throws \ORC\Exception\Exception
	 * @return \ORC\DAO\Table\DataList
	 */
	public function sort($field_name, $asc = true, $convert_charset = false) {
	    $field_names = array_keys($this->_table->getFields());
	    if (!in_array($field_name, $field_names)) {
	        throw new \ORC\Exception\Exception('Unknown Field Name');
	    }
    	$function = function(DataRow $a, DataRow $b) use ($field_name, $asc, $convert_charset){
    	    $v1 = $a->get($field_name);
    	    $v2 = $b->get($field_name);
    	    if ($convert_charset) {
    	        $v1 = iconv('UTF-8', 'GBK//IGNORE', $v1);
    	        $v2 = iconv('UTF-8', 'GBK//IGNORE', $v2);
    	    }
    	    if ($v1 == $v2) return 0;
    	    if ($asc) {
    	        return ($v1 > $v2) ? 1 : -1;
    	    } else {
    	        return ($v1 < $v2) ? 1 : -1;
    	    }
    	};
	    usort($this->_data, $function);
	    return $this;
	}
	
	/**
	 * 按照values里的值进行排序，不在values里的值将被过滤掉
	 * @param string $field_name
	 * @param array $values
	 * @throws \ORC\Exception\Exception
	 * @return \ORC\DAO\Table\DataList
	 */
	public function sortByValue($field_name, array $values) {
	    $field_names = array_keys($this->_table->getFields());
	    if (!in_array($field_name, $field_names)) {
	        throw new \ORC\Exception\Exception('Unknown Field Name');
	    }
	    $data = array();
	    $list = $this->toArray($field_name);
	    foreach ($values as $value) {
	        if (isset($list[$value])) {
	            $data[] = $list[$value];
	        }
	    }
	    $newDataList = clone $this;
	    $newDataList->_data = $data;
	    return $newDataList;
	}
	/**
	 * @see DataList::sortByValue
	 * @param string $field_name
	 * @param array $values
	 * @throws \ORC\Exception\Exception
	 * @return \ORC\DAO\Table\DataList
	 */
	public function sortByField($field_name, array $values) {
	    return $this->sortByValue($field_name, $values);
	}
	
	/**
	 * 如果Key为field_name的值在values里面则返回，否则忽略 
	 * @param string $field_name
	 * @param array $values
	 * @return \ORC\DAO\Table\DataRow[]
	 */
	public function getBy($field_name, array $values) {
	    $result = array();
	    foreach ($this->_data as $row) {
	        if (in_array($row[$field_name], $values)) {
	            $result[$row[$field_name]] = $row;
	        }
	    }
	    return $result;
	}
	
	/**
	 * @see Iterator::current()
	 * @return \ORC\DAO\Table\DataRow
	 */
	public function current() {
		return @$this->_data[$this->_index];
	}

	/**
	 * @see Iterator::key()
	 * @return int
	 */
	public function key() {
		return $this->_index;
	}

	/**
	 * @see Iterator::next()
	 * @return \ORC\DAO\Table\DataRow
	 */
	public function next() {
		++$this->_index;
		return $this->current();
	}

	/* (non-PHPdoc)
	 * @see Iterator::rewind()
	 */
	public function rewind() {
		$this->_index = 0;
	}

	/* (non-PHPdoc)
	 * @see Iterator::valid()
	 */
	public function valid() {
		return isset($this->_data[$this->_index]);
	}
}