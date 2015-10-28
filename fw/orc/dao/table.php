<?php
namespace ORC\DAO;
use ORC\DAO\Exception\Exception;
use ORC\Exception\SystemException;
class Table {
	protected $_table_name;
	protected $_server_name;
	/**
	 * 
	 * @var \ORC\DAO\Table\Schema
	 */
	protected $_schema;
	protected $_pk;
	
	public function __construct($table_name) {
	    list($server_name, $table_name) = Util::parseTableName($table_name);
	    $this->_table_name = $table_name;
	    $this->_server_name = $server_name;
	    $table_name = sprintf('%s.%s', $server_name, $table_name);
		$schema = Schema::getInstance()->getTableSchema($table_name);
		$this->_pk = implode(',', $schema->get('indexes.primary'));
		$this->_schema = $schema;
		if (!$this->_pk) {
			//$this->_pk = 'id';
		}
	}
	
	public function getServerName() {
	    return $this->_server_name;
	}
	
	public function getTableName() {
		return $this->_table_name;
	}
	
	/**
	 * returnt the table name with server name
	 * @return string
	 */
	public function getFullTableName() {
	    return sprintf('%s.%s', $this->getServerName(), $this->getTableName());
	}
	
	public function getPrimaryKey() {
		return $this->_pk;
	}
	
	public function getSchema() {
		return $this->_schema;
	}
	
	public function getFields() {
	    return $this->_schema['fields'];
	}
	
	public function getFilterName($key) {
	    $fields = $this->getFields();
	    foreach ($fields as $field_name => $field) {
	        if ($key == $field_name) {
	            return $field_name;
	        }
	    }
	    foreach ($fields as $field_name => $field) {
	        if ($key == str_replace(array('_', '-'), '', $field_name)) {
	            return $field_name;
	        }
	    }
	    throw new Exception('Wrong field name');
	}
	
	public function filterOrderBy($order_by) {
	    if (empty($order_by)) {
	        return null;
	    }
	    @list($field_name, $scend) = explode(' ', strtolower($order_by), 2);
	    switch (trim($scend)) {
	        case 'desc':
	            $scend = 'DESC';
	            break;
	        default:
	            $scend = 'ASC';
	
	    }
	    if ($field_name == '__default__') {
	        $field_names = array_keys($this->getFields());
	        $has_weight = $has_created = $single_pk = false;
	        foreach ($field_names as $v) {
	            if (strcasecmp('weight', $v) == 0) {
	                $has_weight = true;
	                break;
	            }
	            if (strcasecmp('created', $v) == 0) {
	                $has_created = true;
	            }
	        }
	        if ($has_weight) {
	            $field_name = 'weight';
	        } elseif ($has_created) {
	            $field_name = 'created';
	        } else {
	            $pk = $this->getPrimaryKey();
	            if (strpos(',', $pk) === false) {
	                $field_name = $pk;
	            } else {
	                if ($scend != 'ASC') {
	                    throw new SystemException("Can not use default for this table");
	                }
	            }
	        }
	    }
	    return sprintf('%s %s', $field_name, $scend);
	}
}