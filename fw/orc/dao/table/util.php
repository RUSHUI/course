<?php
namespace ORC\DAO\Table;
class Util {
	/**
	 * 
	 * @param string $class_name
	 * @throws \ORC\DAO\Exception\Exception
	 * @return boolean
	 */
	public static function checkValidDataRowClass($class_name) {
		if (strcasecmp($class_name, '\ORC\DAO\Table\DataRow') == 0) {
			return true;
		}
		$class = new \ReflectionClass($class_name);
		if ($class->isSubclassOf('\ORC\DAO\Table\DataRow')) {
			return true;
		}
		throw new \ORC\DAO\Exception\Exception('Invalid DataRow Class');
	}
	
}