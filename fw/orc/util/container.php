<?php
namespace ORC\Util;
/**
 * Basic container
 * @author Zhou Yanqin
 */
class Container implements \ArrayAccess {

	protected $_data = array();

	public function __set($k, $v) {
		//pre($k, property_exists($this, $k));
		//@todo no idea why have to do this, find this problem when try to set roles in user class
		if ($k == '_data' || (!property_exists($this, $k))) {
			return $this->set($k, $v);
		} else {
			return $this->{$k} = $v;
		}
	}

	public function __get($k) {
		//pre($k);
		return $this->get($k);
	}

	public function __isset($k) {
		return $this->exists($k);
	}

	public function __unset($k) {
		$this->remove($k);
	}

	public function getAllData() {
		return $this->_data;
	}

	public function removeAll() {
		$this->_data = array();
	}
	/**
	 * get a value
	 * @param mixed $k
	 * @return mixed
	 */
	public function get($k) {
		return isset($this->_data[$k]) ? $this->_data[$k] : null;
	}

	/**
	 * set a value
	 * @param string $k
	 * @param mixed $v
	 * @return mixed the value
	 */
	public function set($k, $v) {
		return $this->_data[$k] = $v;
	}

	/**
	 * append a value behind exist one
	 * if the old value is an array, will add a new value to the value
	 * or append the value
	 * @param string $k
	 * @param mixed $v
	 * @retun mixed the value
	 */
	public function append($k, $v) {
		if (!$this->exists($k)) {
			return $this->set($k, $v);
		}
		$v_old = $this->get($k);
		if (!is_array($v_old)) {
			$v = $v_old . $v;
		} else {
			$v = array_merge($v_old, $v);
		}
		return $this->set($k, $v);
	}

	/**
	 * check whether data exists
	 * notice that even the value is null, it may return true if you set it to null
	 * @param string $k
	 * @return bool
	 */
	public function exists($k) {
		return array_key_exists($k, $this->_data);
	}

	/**
	 * remove a value
	 * @param string $k
	 */
	public function remove($k) {
		unset($this->_data[$k]);
	}
	
	/* (non-PHPdoc)
	 * @see ArrayAccess::offsetExists()
	 */
	public function offsetExists($offset) {
		return $this->exists($offset);
	}

	/* (non-PHPdoc)
	 * @see ArrayAccess::offsetGet()
	 */
	public function offsetGet($offset) {
		return $this->get($offset);
	}

	/* (non-PHPdoc)
	 * @see ArrayAccess::offsetSet()
	 */
	public function offsetSet($offset, $value) {
		if (is_null($offset)) {
			$offset = $this->getMaxIndex();
		}
		return $this->set($offset, $value);
	}

	/* (non-PHPdoc)
	 * @see ArrayAccess::offsetUnset()
	 */
	public function offsetUnset($offset) {
		$this->remove($offset);
	}

	protected function getMaxIndex() {
	    $max_index = -1;
	    foreach ($this->_data as $key => $value) {
	        if (is_int($key)) {
	            if ($key > $max_index) {
	                $max_index = $key;
	            }
	        }
	    }
	    return ++ $max_index;
	}
	
}