<?php
namespace ORC\DAO;
use ORC\Core\CacheFactory;
use ORC\DBAL\DBAL;
use ORC\Core\Cache\ICacher;
use ORC\DAO\Table\DataList;
class Util {
    public static function parseTableName($table_name) {
        if(strpos($table_name, '.')) {
            list($server_name, $table_name) = explode('.', $table_name, 2);
        } else {
            $server_name = Config::DEFAULT_NAME;
        }
        return array($server_name, $table_name);
    }
    
    /**
     * 
     * @param string $table_name
     * @param bool $cache
     * @return \ORC\DAO\Table\DataList
     */
    public static function getTableData($table_name, $cache = true) {
        if ($cache) {
            $cacheKey = sprintf('table_%s', $table_name);
            $cacher = CacheFactory::get(null, 'db');
            $data = $cacher->get($cacheKey);
            if (is_object($data)) {
                return $data;
            }
            if (@strcmp($data, ICacher::EMPTY_VALUE) == 0) {
                return new DataList(array(), new Table($table_name));
            }
        }
        $dbal = DBAL::select($table_name);
        $list = $dbal->execute();
        if ($cache) {
            if ($list) {
                $cacher->set($cacheKey, $list);
            } else {
                $cacher->set($cacheKey, ICacher::EMPTY_VALUE);
            }
        }
        return $list;
    }
    
    public static function flushTableDataCache($table_name) {
        $cacheKey = sprintf('table_%s', $table_name);
        $cacher = CacheFactory::get(null, 'db');
        $cacher->delete($cacheKey);
//         pre($cacher, $cacheKey);
        return true;
    }
}