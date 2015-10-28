<?php
namespace ORC\DAO\Table;
use ORC\DAO\Table;
class DataRow extends \ORC\Util\Container{
	protected $_table;
	public function setTable(Table $table) {
		$this->_table = $table;
	}
	
	/**
	 * 
	 * @return Table
	 */
	public function getTable() {
		return $this->_table;
	}
}