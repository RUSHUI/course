<?php
namespace ORC\Util;
/**
 * this is a case-insensitive container, so all keys will be case-insensitive
 * @author 彦钦
 *
 */
class CaseContainer extends Container {
    public function get($key) {
        $key = $this->findKey($key);
        if ($key === false) {
            return null;
        }
        return parent::get($key);
    }
    
    public function set($key, $value) {
        $k = $this->findKey($key);
        if ($k === false) {
            $k = $key;
        }
        return parent::set($k, $value);
    }
    
    public function exists($key) {
        if ($this->findKey($key) === false) {
            return false;
        }
        return true;
    }
    
    public function remove($key) {
        $key = $this->findKey($key);
        if ($key === false) {
            return;
        }
        return parent::remove($key);
    }
    
    protected function findKey($key) {
        if (is_int($key)) {
            if (isset($this->_data[$key])) {
                return $key;
            } else {
                return false;
            }
        }
        foreach ($this->_data as $k => $v) {
            if (strcasecmp($k, $key) == 0) {
                return $k;
            }
        }
        return false;
    }
}