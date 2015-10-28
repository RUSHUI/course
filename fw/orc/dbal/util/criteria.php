<?php
namespace ORC\DBAL\Util;
use ORC\DAO\Table;
class Criteria {
	protected $_tables;
	protected $_db;
	public function __construct(\ORC\DBAL\Common\Common $dbal) {
		
	}
	
	public function addTable(Table $table, $alias = null) {
		if ($alias == null) {
			$alias = $table->getTableName();
		}
		$this->_tables[$alias] = $table;
		return $this;
	}
	
	/**
	 * 对于一条sql，检测字段等是否符合要求
	 * @param string $sql
	 * @return boolean true means sql is valid
	 * @todo not implemented yet
	 */
	public function validSQL($sql) {
		
	}
	
	public function __toString() {
		
	}
}