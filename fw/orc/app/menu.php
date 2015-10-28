<?php
namespace ORC\APP;
class Menu {
    private static $instances = array();
    public static function getMenu($name) {
        if (isset(self::$instances[$name])) {
            return self::$instances[$name];
        }
        self::$instances[$name] = new self();
        return self::$instances[$name];
    }
    
    private $_items = array();
    private function __construct() {
        
    }
    
    public function addMenuItem(\ORC\APP\Menu\Item $item) {
        $this->_items[] = $item;
    }
    
}