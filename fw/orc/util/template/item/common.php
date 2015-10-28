<?php
namespace ORC\Util\Template\Item;
abstract class Common {
    protected $_module_name;
    protected $_type;
    protected $_extra;
    public function __construct($item) {
        list($module_name, $type, $extra) = explode('.', $item, 3);
        $this->_module_name = $module_name;
        $this->_type = $type;
        $this->_extra = $extra;
    }
    
    public function getType() {
        return $this->_type;
    }
    
    public function getModuleName() {
        return $this->_module_name;
    }
}