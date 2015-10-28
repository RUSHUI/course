<?php
namespace ORC\Helpers;
use ORC\Util\UUID;
use ORC\DAO\Table;

class Form {
	protected $form_name;
	public function __construct($form_name) {
		if (!in_array($form_name, $this->getExistingForms())) {
			//form does not exists
		}
		$this->form_name = $form_name;
	}
	
	public function render() {
		
	}
	
	/**
	 * @return void
	 */
	public function display() {
		echo $this->render();
	}
	
	public function generate() {
		
	}
	
	/**
	 * get existing form names
	 * @return array
	 */
	protected function getExistingForms() {
		return array();
	}
}

/**
 * @deprecated
 * @author Zhou Yanqin
 */
class Form_old {
	protected $_id;
	protected $_tables;
	public function __construct(Table $table, $alias = null) {
		$this->_id = UUID::guid();
		if ($alias == null) {
			$alias = $table->getTableName();
		}
		$this->_tables[$alias] = $table;
	}
	
	public function addTable(Table $table, $alias = null) {
		if ($alias == null) {
			$alias = $table->getTableName();
		}
		$this->_tables[$alias] = $table;
		return $this;
	}
	
	public function valid() {
		
	}
	
	public function prepare() {
		
	}
	
	public function render() {
		$output = '<form id="' . $this->_id . '"';
		return $output;
	}
	
	protected function getAllFields() {
		
	}
	
	protected function getTemplate($field) {
		
	}
}