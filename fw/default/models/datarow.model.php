<?php
use ORC\DAO\Table;
class Default_DataRow_Model extends \ORC\MVC\Model {
	protected $_table;
	
	public function setTable($table) {
		if (is_string($table)) {
			$this->_table = new Table($table);
		} elseif ($table instanceof \ORC\DAO\Table) {
			$this->_table = $table;
		} else {
			throw new \ORC\Exception\SystemException('Wrong param');
		}
	}
}