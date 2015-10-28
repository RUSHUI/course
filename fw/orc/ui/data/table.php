<?php
namespace ORC\UI\Data;
/**
 * @TODO
 * @author 彦钦
 *
 */
class Table implements \Iterator {
    protected $_callback;
    protected $_row_callback;
    protected $_rows = array();
    protected $_index = 0;
    public function __construct() {
    }
    public function setCallBack(callable $callback) {
        $this->_callback = $callback;
    }

    public function setRowCallBack(callable $callback) {
        $this->_row_callback = $callback;
    }
    
    public function loadData($data) {
        if ($data instanceof \ORC\DAO\Table\DataList) {
            $this->convertFromDataList($data);
        }
    }
    
    protected function convertFromDataList(\ORC\DAO\Table\DataList $data) {
        foreach($data as $row) {
            //convert dao datarow to ui row
            
        }
    }
    
    
    /* (non-PHPdoc)
     * @see Iterator::current()
     */
    public function current() {
        return @$this->_rows[$this->_index];
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
        return isset($this->_rows[$this->_index]);
    }    
}