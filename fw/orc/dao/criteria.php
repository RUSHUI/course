<?php
namespace ORC\DAO;
/**
 * @deprecated should not be used anymore. Use DBAL instead.
 * @author Zhou Yanqin
 *
 */
class Criteria {
	protected $_table;
	public function __construct() {
	}
	
	public function setTable(Table $table) {
		if (!isset($this->_table)) {
			$this->_table = $table;
		}
		throw new Exception\Exception('Table for Criteria already set');
	}
	
	public function hasTable() {
		return isset($this->_table);
	}
	
	public function prepare(Dao $dao) {
		
	}
	
	/**
	 * @return string the sql
	 */
	public function __toString() {
		
	}
}