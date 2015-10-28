<?php
namespace ORC\Util\Template;
class Content extends Common {
    protected $_before = array();
    protected $_after = array();
    public function __construct(array $before, array $after) {
        foreach ($before as $item) {
            $this->_before[] = $this->createItem($item);
        }
        foreach ($after as $item) {
            $this->_after[] = $this->createItem($item);
        }
    }
    
    public function getBefore() {
        return $this->_before;
    }
    
    public function getAfter() {
        return $this->_after;
    }
}