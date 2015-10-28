<?php
namespace ORC\UI\Data;
class Row implements \Iterator {
    protected $_callback;
    protected $_objects = array();
    public function __construct($data, callable $callback = null) {
        if ($callback) {
            $this->_callback = $callback;
        }
        if ($data instanceof \ORC\DAO\Table\DataRow) {
            $this->convertFromDataRow($data);
        }
    }
    
    protected function convertFromDataRow(\ORC\DAO\Table\DataRow $row) {
        
    }
    /* (non-PHPdoc)
     * @see Iterator::current()
     */
    public function current() {
        return @$this->_objects[$this->_index];
    }
    
    /* (non-PHPdoc)
     * @see Iterator::key()
     */
    public function key() {
        return $this->_index;
    }
    
    /* (non-PHPdoc)
     * @see Iterator::next()
     */
    public function next() {
        ++$this->_index;
        return $this->current();
    }
    
    /* (non-PHPdoc)
     * @see Iterator::rewind()
     */
    public function rewind() {
        $this->_index = 0;
    }
    
    /* (non-PHPdoc)
     * @see Iterator::valid()
     */
    public function valid() {
        return isset($this->_objects[$this->_index]);
    }
}