<?php 
namespace ORC\DBAL;
use ORC\DAO\Table;
use ORC\DAO\Util;
use ORC\DAO\Config;
final class DBAL {

	private function __construct() {
	    
	}
	
	const SELECT = 'select';
	const UPDATE = 'update';
	const DELETE = 'delete';
	const INSERT = 'insert';
	/**
	 * 
	 * @param string/\ORC\DAO\Table $table
	 * @param string $op
	 * @return \ORC\DBAL\Common\Common
	 */
	protected static function create($table, $op = self::SELECT) {
		if (is_string($table)) {
			$table = new Table($table);
		}
		if ($table instanceof Table) {
			//first find the engine
			$engine = self::getEngine($table->getServerName());
			$class_name = "\\ORC\\DBAL\\" . $engine . "\\" . $op;
			return new $class_name($table);
		}
	}
	
	public static function getEngine($server_name) {
	    $server_info = Config::getInstance()->getServerInfo($server_name);
	    return $server_info['engine'];
	}
	/**
	 * 
	 * @param string/\ORC\DAO\Table $table
	 * @return \ORC\DBAL\Common\Update
	 */
	public static function update($table) {
		return self::create($table, self::UPDATE);
	}
	
	/**
	 * 
	 * @param string/\ORC\DAO\Table $table
	 * @return \ORC\DBAL\Common\Delete
	 */
	public static function delete($table) {
		return self::create($table, self::DELETE);
	}
	
	/**
	 * 
	 * @param string/\ORC\DAO\Table $table
	 * @return \ORC\DBAL\Common\Select
	 */
	public static function select($table) {
		return self::create($table, self::SELECT);
	}

	/**
	 * 
	 * @param string/\ORC\DAO\Table $table
	 * @return \ORC\DBAL\Common\Insert
	 */
	public static function insert($table) {
		return self::create($table, self::INSERT);
	}
}