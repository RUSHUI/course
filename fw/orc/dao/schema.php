<?php
namespace ORC\DAO;
use Symfony\Component\Yaml\Yaml;
use ORC\Util\Logger;
use ORC\Core\CacheFactory;
Class Schema {
	protected static $_instance;
	protected $_schemas;
	
	/**
	 *
	 * @return \ORC\DAO\Schema
	 */
	public static function getInstance() {
		if (!isset(self::$_instance) || !is_object(self::$_instance)) {
			self::$_instance = new static();
		}
		return self::$_instance;
	}
	
	
	protected function __construct() {
		$cacher = CacheFactory::get('config_cache', 'schema');
		//pre($cacher);
		$data = $cacher->get('schemas');
		if (empty($data)) {
		    //pre($cacher->getAllKeys(), $data);
			$this->retrieveSchema();
            $cacher->set('schemas', $this->_schemas, 0);
		} else {
			$this->_schemas = $data;
		}
	}
	
	/**
	 * 
	 * @param string $table_name
	 * @return \ORC\DAO\Table\Schema;
	 * @throws \ORC\DAO\Exception\Exception
	 */
	public function getTableSchema($table_name) {
	    list($server_name, $table_name) = $this->parseTableName($table_name);
		if (isset($this->_schemas[$server_name][$table_name])) {
			return $this->_schemas[$server_name][$table_name];
		}
		throw new \ORC\Exception\SystemException('Unknown table', $table_name);
	}
	
	public function getSchemas() {
		return $this->_schemas;
	}
	protected function retrieveSchema() {
	    $config = Config::getInstance();
	    $servers = $config->getServers();
	    foreach ($servers as $server_name => $server_info) {
	        $folder = DIR_APP_ROOT . DIRECTORY_SEPARATOR . 'schema' . DIRECTORY_SEPARATOR . $server_name . DIRECTORY_SEPARATOR;
	        $this->_parseYml($folder, $server_name);
	    }
	}
	
	protected function parseTableName($table_name) {
	    return Util::parseTableName($table_name);
	}
	
	protected function _parseYml($folder, $server_name) {
		Logger::getInstance('DAO')->addNotice("parseYml() called in Schema for $server_name");
		$d = dir($folder);
		$this->_schemas[$server_name] = array();
		while (false !== ($entry = $d->read())) {
			if ($entry == '.' || $entry == '..') continue;
			if (is_dir($folder . DIRECTORY_SEPARATOR . $entry)) continue;
			//check extension
			list($table_name, $extension) = explode('.', $entry);
			if ($extension == 'yml') {
				$filename = $folder . $entry;
				$schema = Yaml::parse($filename);
				if ($schema['name'] != $table_name) {
					throw new \ORC\Exception\SystemException("Schema file $entry doesn't match the table name!");
				}
				$extra_filename = $folder . DIRECTORY_SEPARATOR . 'extra' . DIRECTORY_SEPARATOR . $table_name . '.yml';
				if (file_exists($extra_filename)) {
					$extra = Yaml::parse($extra_filename);
					if (!empty($extra)) {
						$schema['extra info'] = $extra;//use a value can not be used as field name
						//pre($extra);
					}
				}
				$this->_schemas[$server_name][$table_name] = new Table\Schema($schema);
			}
		}
		$d->close();
	}
}