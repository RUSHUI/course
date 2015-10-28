<?php
namespace ORC\DAO\Criteria;
class id extends \ORC\DAO\Criteria {
	private $_id;
	public function __construct($id) {
		$this->_id = $id;
	}
	
	public function __toString() {
		if (!$this->hasTable()) {
			throw new \ORC\Exception\Exception('Table not setted.');
		}
		
	}
}