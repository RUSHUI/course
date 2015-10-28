<?php
namespace ORC\Util;
class Callback {
	public static function staticcall($func) {
		$args = func_get_args();
		array_shift($args);
		if (is_string($func)) {
			if (strpos($func, '::')) {
				list($class_name, $method_name) = explode('::', $func);
				if (method_exists($class_name, $method_name)) {
					return call_user_func_array($func, $args);
				}
			} else {
				if (function_exists($func)) {
					return call_user_func_array($func, $args);
				}
			}
		} elseif (is_array($func)) {
			$obj = $func[0];
			$method_name = $func[1];
			if (method_exists($obj, $method_name)) {
				return call_user_func_array($func, $args);
			}
		}
		return null;
	}
	
	
	protected static $instances = array();
	protected $_callbacks = array();
	/**
	 * 
	 * @param string $name
	 * @return \ORC\Util\Callback
	 */
	public static function get($name) {
		if (!isset(self::$instances[$name])) {
			self::$instances[$name] = new static();
		}
		return self::$instances[$name];
	}
	
	protected function __construct() {
		
	}
	
	public function register($callback) {
		if (false !== ($key = array_search($callback, $this->_callbacks))) {
			unset($this->_callbacks[$key]);
		}
		$this->_callbacks[] = $callback;//makes the callback at the bottom of the lis
		if ($key !== false) {
			$this->_callbacks = array_values($this->_callbacks);
		}
	}
	
	public function unregister($callback) {
		if (false !== ($key = array_search($callback, $this->_callbacks))) {
			unset($this->_callbacks[$key]);
			$this->_callbacks = array_values($this->_callbacks);
		}
	}
	
	public function call() {
		//pre($this->_callbacks);
		$args = func_get_args();
		foreach ($this->_callbacks as $func) {
			if (is_string($func)) {
				if (strpos($func, '::')) {
					list($class_name, $method_name) = explode('::', $func);
					if (method_exists($class_name, $method_name)) {
						return call_user_func_array($func, $args);
					}
				} else {
					if (function_exists($func)) {
						return call_user_func_array($func, $args);
					}
				}
			} elseif (is_array($func)) {
				$obj = $func[0];
				$method_name = $func[1];
				if (method_exists($obj, $method_name)) {
					return call_user_func_array($func, $args);
				}
			}
		}
	}
}