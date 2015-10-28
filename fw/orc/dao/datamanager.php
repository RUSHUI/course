<?php
namespace ORC\DAO;
/**
 * @deprecated not finished and should not be used. use dbal instead
 * @author Zhou Yanqin
 *
 */
final class DataManager {
	private static $_tables = array();
	/**
	 * 
	 * @param string $table_name
	 * @return \ORC\DAO\DataManager
	 */
	public static function getManager($table_name) {
		if (!isset(self::$_tables[$table_name])) {
			self::$_tables[$table_name] = new static(new Table($table_name));
		}
		return self::$_tables[$table_name];
	}
	
	private $_table;
	private function __construct(Table $table) {
		$this->_table = $table;
	}
	
	public function getOne(Criteria $criteria, $class_name = '\ORC\DAO\Table\DataRow') {
		if (!$criteria->hasTable()) {
			$criteria->setTable($this->_table);
		}
	}
	
	public function getOneOld($id, $class_name = '\ORC\DAO\Table\DataRow') {
		$this->checkValidDataRowClass($class_name);
		$dao = DaoFactory::get();
		$sql = 'SELECT * FROM ' . $this->_table->getTableName() . ' WHERE ' . $this->_table->getPrimaryKey() . ' = :id';
		$dao->prepare($sql);
		$dao->bindValue(':id', $id, $this->getDataType($this->_table->getPrimaryKey()));
		$dao->execute();
		$dao->setFetchMode(DAO::FETCH_CLASS | DAO::FETCH_PROPS_LATE, $class_name);
		$data = $dao->fetch(null);
		if (!empty($data)) {
			$data->setTable($this->_table);
		}
		return $data;
	}
	
	public function getMany(array $ids, $class_name = '\ORC\DAO\Table\DataRow') {
		$this->checkValidDataRowClass($class_name);
		$dao = DaoFactory::get();
		$sql = 'SELECT * FROM ' . $this->_table->getTableName() . ' WHERE ' . $this->_table->getPrimaryKey() . ' in (' . implode(',', array_fill(0, count($ids), '?')) . ')';
		$dao->prepare($sql);
		$dao->execute($ids);
		$dao->setFetchMode(DAO::FETCH_CLASS | DAO::FETCH_PROPS_LATE, $class_name);
		$data = $dao->fetchAll(null);
		if ($data) {
			$data = new Table\DataList($data, $this->_table);
		}
		return $data;
	}
	
	public function deleteOne($id) {
	}
	
	public function deleteMany(array $ids) {
		
	}
	
	/**
	 * @todo
	 * @param unknown $id
	 * @param unknown $op
	 * @throws Exception\Exception
	 */
	protected function getOperationForOne($id, $op) {
		switch (strtolower($op)) {
			case 'select':
				$sql = 'SELECT *';
				break;
			case 'delete':
				$sql = 'DELETE';
				break;
			default:
				throw new Exception\Exception('Unknown op');
		}
		$sql .= ' FROM ' . $this->_table->getTableName() . ' WHERE ' . $this->_table->getPrimaryKey() . ' = :id';
	}
	protected function getDataType($field_name) {
		$field = $this->_table->getSchema()->get('fields.' . $field_name);
		if (!$field) {
			throw new \ORC\DAO\Exception\Exception('Unknown field');
		}
		switch ($field['type']) {
			case 'int':
			case 'smallint':
			case 'bigint':
			case 'tinyint':
			case 'mediumint':
				//case 'float':
				//case 'double':
				return Dao::PARAM_INT;
				break;
			case 'blob':
			case 'tinyblob':
			case 'smallblob':
			case 'mediumblob':
			case 'longblog':
				return DAO::PARAM_LOB;
				break;
			default:
				return DAO::PARAM_STR;
		}
	}
	
	protected function checkValidDataRowClass($class_name) {
		return Table\Util::checkValidDataRowClass($class_name);
	}
}