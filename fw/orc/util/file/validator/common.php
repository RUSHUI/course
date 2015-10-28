<?php
namespace ORC\Util\File\Validator;
abstract class Common {
	protected $_error;
	
	public function getError() {
		return $this->_error;
	}
	
	public function getName() {
		return get_class($this);
	}
}