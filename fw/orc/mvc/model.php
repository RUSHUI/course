<?php
namespace ORC\MVC;
use ORC\Util\Container;
use ORC\DAO\Dao;
use ORC\DAO\DaoFactory;
class Model extends Container{
    protected $_controller;
	public function __construct(Controller $c) {
		$this->_controller = $c;
	}
	
	protected function raiseError($message, Dao $dao = null) {
	    $this->set('error_message', $message);
	    if ($dao && $dao->inTransaction()) {
	        $dao->rollBack();
	    }
	    return false;
	}
}