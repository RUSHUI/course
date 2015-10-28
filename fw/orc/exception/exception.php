<?php
namespace ORC\Exception;
class Exception extends \Exception {
    protected $_title;
    protected $_debug;
    public function __construct($message, $debug_info = null, \Exception $previous = null) {
        parent::__construct($message, $this->retrieveCode(), $previous);
        $this->_debug = $debug_info;
    }
	
	public function getTitle() {
		if (empty($this->_title)) {
			$class_name = get_class($this);
			//@todo not implemented yet
		}
	}
	
	public function getDebugInfo() {
	    return $this->_debug;
	}
	protected function retrieveCode() {
	    //@todo get the code
	    return 1;
	}
}