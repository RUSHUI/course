<?php
namespace ORC\Util;
class Observer {
	protected static $instances = array();
	
	/**
	 * @param string $name
	 * @return \ORC\Util\Observer
	 */
	public static function getObserver($name) {
		if (!isset(self::$instances[$name])) {
			self::$instances[$name] = new static();
		}
		return self::$instances[$name];
	}
	
	protected $_observers = array();
	protected function __construct() {
		
	}
	
	public function register(Observer\Observer $observer) {
		if (false === array_search($observer, $this->_observers, true)) {
			$this->_observers[] = $observer;
		}
	}
	
	public function unregister(Observer\Observer $observer) {
		if (false !== ($index = array_search($observer, $this->_observers, true))) {
			unset($this->_observers[$index]);
			$this->_observers = array_values($this->_observers);
		}
	}
	
	public function notify() {
		foreach ($this->_observers as $observer) {
			$observer->notify();
		}
	}
}