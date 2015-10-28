<?php
use ORC\DAO\DaoFactory;
use ORC\DAO\Dao;
use ORC\Core\Config;
use Symfony\Component\Yaml\Yaml;
/**
 * @TODO need to rewrite after the big change with database
 * @author 彦钦
 *
 */
class Schema {
	private $_ignore_exists;
	private $_yes_to_all;
	private $_daos = array();
	private $_servers;
	private $_table_names = array();
	private $_exists = array();
	private $_schema_roots;
	
	public function __construct($ignore_exists = false, $yes_to_all = false) {
		$this->_ignore_exists = $ignore_exists;
		$this->_yes_to_all = $yes_to_all;
	}
	
	public function createSchema() {
		$this->getTables();
		if ($this->_ignore_exists == false) {
			$this->getExistingSchema();
		}
		//var_dump($this->_table_names, $this->_exists);exit();
		foreach ($this->_table_names as $server_name => $tables) {
		    foreach ($tables as $table_name) {
    			$confirmed = false;
    			if ($this->_ignore_exists == false) {
    				if (isset($this->_exists[$server_name][$table_name])) {
    					if ($this->_exists[$server_name][$table_name] == 0) {
    						//exists and not changed
    						continue;
    					} else {
    						$confirmed = $this->confirm($table_name, true);
    					}
    				} else {
    					$confirmed = $this->confirm($table_name, false);
    				}
    			} else {
    				$confirmed = $this->confirm($table_name, false);
    			}
    			if ($confirmed) {
    				//create the table schema
    				$this->create($server_name, $table_name);
    			}
		    }
		}
	}
	
	private function create($server_name, $table_name) {
		$schema_root = $this->getSchemaRoot($server_name);
		$filename = sprintf('%s%s.yml', $schema_root, $table_name);
		$schema = $this->getTableSchema($server_name, $table_name);
		//var_dump($schema);
		$schema['generated'] = time();
// 		var_dump($filename, Yaml::dump($schema, 99));return;
		if (file_put_contents($filename, Yaml::dump($schema, 99))) {
			printf("文件%s/%s生成成功！\n", $server_name, $table_name);
		} else {
			printf("文件%s/%s生成失败！\n", $server_name, $table_name);
		}
	}
	
	private function getTableSchema($server_name, $table_name) {
		$schema = array();
		$schema['name'] = $table_name;
		$schema['fields'] = array();
		$schema['indexes'] = array();
		$dao = $this->getDao($server_name);
		$dao->query('describe ' . $table_name);
		while ($row = $dao->fetch()) {
			$field_name = $row['Field'];
			$field = array();
			$type = $row['Type'];
			@list($field_type, $field_size, $extra) = preg_split('/[()]/', $type, 3);
			if (!isset($field_size)) {
				$field_type = $type;
				$field['type'] = $field_type;
			} else {
				$field['type'] = $field_type;
				$field['size'] = (int)$field_size;
			}
			switch ($field_type) {
				case 'enum':
					$v = explode(',', $field_size);
					foreach ($v as $kk => $vv) {
						$v[$kk] = trim($vv, "\t\n\r\0\x0B'");//strip the useless '
					}
					$field['value'] = $v;
					//var_dump($field['value']);
					unset($field['size']);
					break;
			}
			if(!empty($extra)) {
				$extra = trim($extra);
				foreach (explode(' ', $extra) as $v) {
					if ($v == 'unsigned') {
						$field['unsigned'] = true;
					} elseif ($v == 'zerofill') {
						$field['zerofill'] = true;
					}
				}
			}			
			$field['notnull'] = $row['Null'] == 'NO' ? true : false;
			//if ($row['Key'] == 'PRI') {
			//	$schema['indexes']['primary'][] = $field_name;
			//}
			$field['default'] = $row['Default'] == 'NULL' ? null : $row['Default'];
			if (!empty($row['Extra'])) {
				$field['extra'] = $row['Extra'];
			}
			$schema['fields'][$field_name] = $field;
		}
		//update index
		$dao->query('SHOW INDEXES FROM ' . $table_name);
		while ($row = $dao->fetch()) {
			if ($row['Key_name'] == 'PRIMARY') {
				$schema['indexes']['primary'][] = $row['Column_name'];
			}
			//@todo other index is not useful at all
		}
		return $schema;
	}
	
	private function confirm($table_name, $exists) {
	    if ($this->_yes_to_all) {
	        return true;
	    }
		if ($exists) {
			echo "表" . $table_name . "已经存在，是否要重新创建Schema文件？\n";
		} else {
			echo "是否要为表" . $table_name . "创建Schema文件？\n";
		}
		$line = readline("确认请输入Y/y，否则输入其他任意键:");
		if ($line != 'y' && $line != 'Y') {
			echo "不创建Schema文件。 \n";
			return false;
		}
		return true;
	}
	
	private function getTables() {
	    //first get all servers
	    $servers = $this->getServers();
	    foreach ($servers as $server_name => $server_info) {
	        $dao = $this->getDao($server_name);
	        $dao->query('show tables');
	        $this->_table_names[$server_name] = array();
	        while ($row = $dao->fetch(Dao::FETCH_NUM)) {
	            $table_name = $row[0];
// 	            if (substr($table_name, 0, 5) == 'orc__') {
// 	                continue;//ignore the framework table
// 	            }
	            $this->_table_names[$server_name][] = $table_name;
	        }
	    }
	}
	
	private function getExistingSchema() {
	    $servers = $this->getServers();
	    foreach ($servers as $server_name => $server_info) {
	        $this->_exists[$server_name] = array();
    		$schema_root = $this->getSchemaRoot($server_name);
    		$d = dir($schema_root);
    		while (false !== ($entry = $d->read())) {
    			if ($entry == '.' || $entry == '..') continue;
    			if (is_dir($schema_root . DIRECTORY_SEPARATOR . $entry)) continue;
    			//check extension
    			list($table_name, $extension) = explode('.', $entry);
    			if ($extension == 'yml') {
    				$filename = $schema_root . $entry;
    				$schema = Yaml::parse($filename);
    				if ($schema['generated'] == filemtime($filename)) {
    					$this->_exists[$server_name][$table_name] = 0;//文件存在而且没有动过
    				} else {
    					$this->_exists[$server_name][$table_name] = 1;//文件存但是动过
    				}
    			}
    		}
    		$d->close();
	    }
	}
	
	private function getSchemaRoot($server_name) {
		if (!isset($this->_schema_roots[$server_name])) {
			$this->_schema_roots[$server_name] = DIR_APP_ROOT . DIRECTORY_SEPARATOR . 'schema' . DIRECTORY_SEPARATOR . $server_name . DIRECTORY_SEPARATOR;
			if (!file_exists($this->_schema_roots[$server_name])) {
			    mkdir($this->_schema_roots[$server_name]);
			}
		}
		return $this->_schema_roots[$server_name];
	}
	
	private function getServers() {
	    if (!isset($this->_servers)) {
	        $this->_servers = \ORC\DAO\Config::getInstance()->getServers();
	    }
	    return $this->_servers;
	}
	private function getDao($server_name) {
	    if (!isset($this->_daos[$server_name])) {
	        $this->_daos[$server_name] = DaoFactory::create($server_name);
	    }
	    return $this->_daos[$server_name];
	}
}