<?php
namespace ORC\Util;
class Timer {
    private $_name;
    private $_engine;
    private $_end_time;
    public function __construct($name, $engine) {
        $this->_name = $name;
        $this->_engine = $engine;
        //get the end time
        $storage = $this->getStorage();
        $this->_end_time = $storage->get($name);
    }
    
    public function reset($expiration) {
        $this->_end_time = Util::getNow() + $expiration;
        $this->getStorage()->set($this->_name, $this->_end_time);
    }
    
    public function current() {
        if ($this->_end_time) {
            $time_last = $this->_end_time - Util::getNow();
            if ($time_last > 0) {
                return $time_last;
            }
        }
        return 0;
    }
    
    private function getStorage() {
        return StorageFactory::getStorage($this->_engine, '__orc__timer');
    }
}