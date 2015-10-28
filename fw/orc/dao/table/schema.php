<?php
namespace ORC\DAO\Table;
class Schema extends \ORC\Util\AdvancedContainer {
	public function __construct(array $schema) {
		$this->_data = $schema;
	}
	
	public function set($k, $v) {
		throw new \ORC\Exception\Exception('can not change a existing schema');
	}
}