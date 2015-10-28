<?php
namespace ORC\Util\Template;
class Block extends Common {
    protected $_items = array();
    public function __construct(array $items) {
        foreach ($items as $item) {
            $this->_items[] = $this->createItem($item);
        }
    }
    
    public function getItems() {
        return $this->_items;
    }
    
    public function addItem($item_name) {
        $this->_items[] = $this->createItem($item_name);
    }
}