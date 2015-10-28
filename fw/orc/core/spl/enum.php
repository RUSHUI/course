<?php
namespace ORC\Core\SPL;
use ORC\Exception\SystemException;
abstract class Enum {
    /**
     * 使用static保证整个生命周期内数据只有一份
     * @var unknown
     */
    private static $constants = array();
    private $value;
    public function __construct($v) {
        $this->populateConstants();
        $class = get_class($this);
        if (!in_array($v, self::$constants[$class], true)) {
            throw new SystemException("错误的数据", $v, new \UnexpectedValueException("Value is not in enum" . $class));
        }
        $this->value = $v;
    }
    
    public function __toString() {
        return (string) $this->value;
    }
    
    public function getValue() {
        return $this->value;
    }
    
    public function equals($obj) {
        if (get_class($obj) != get_class($this)) {
            return false;
        }
        return $this->value === $obj->value;
    }
    
    private function populateConstants() {
        $class = get_class($this);
        if (isset(self::$constants[$class])) {
            return;
        }
        $r = new \ReflectionClass($class);
        $constants = $r->getConstants();
        self::$constants[$class] = $constants;
    }
}