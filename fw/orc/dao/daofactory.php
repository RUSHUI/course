<?php
namespace ORC\DAO;
class DaoFactory {
    const DEFAULT_NAME = CONFIG::DEFAULT_NAME;
	private static $_daos = array();
	private function __construct() {}
	
	/**
	 * @param string $name server name
	 * @return \ORC\DAO\Dao
	 */
	public static function create($name = self::DEFAULT_NAME) {
	    $config = Config::getInstance();
	    $server_info = $config->getServerInfo($name);
	    if (!isset($server_info['dbpass'])) {
	        $server_info['dbpass'] = null;
	    }
	    if (!isset($server_info['options'])) {
	        $server_info['options'] = array();
	    }
		$dao = new Dao($server_info);
		if (!isset(self::$_daos[$name])) {
		    self::$_daos[$name] = array();
		}
		self::$_daos[$name][] = $dao;
		return $dao;
	}
	
	/**
	 * will always get the same DAO object
	 * @param string $name server name
	 * @return \ORC\DAO\Dao
	 */
	public static function get($name = self::DEFAULT_NAME) {
	    if (empty($name)) {
	        $name = self::DEFAULT_NAME;
	    }
	    if (!isset(self::$_daos[$name])) {
	        self::$_daos[$name] = array();
	    }
		if (count(self::$_daos[$name]) == 0) {
			return self::create($name);
		}
		foreach (self::$_daos[$name] as $dao) {
			return $dao;
		}
	}
}