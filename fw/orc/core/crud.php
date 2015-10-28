<?php
namespace ORC\Core;
class CRUD {
    const SORT_DEFAULT_ASC = '__default__ asc';
    const SORT_DEFAULT_DESC = '__default__ desc';
    
    const CACHE_PREFIX_ITEM = 'item';
    const CACHE_PREFIX_ALL = 'all';

    protected static function createInstance($type, $table) {
        $class_name = "\\ORC\\Core\\CRUD\\" . $type;
        $obj = new $class_name($table);
        return $obj;
    }
    
    /**
     * @param string $table
     * @return \ORC\Core\CRUD\Create
     */
    public static function create($table) {
        return self::createInstance('create', $table);
    }
    
    /**
     * @param string $table
     * @return \ORC\Core\CRUD\Read
     */
    public static function read($table) {
        return self::createInstance('read', $table);
    }
    
    /**
     * @param string $table
     * @return \ORC\Core\CRUD\Update
     */
    public static function update($table) {
        return self::createInstance('update', $table);
    }
    
    /**
     * @param string $table
     * @return \ORC\Core\CRUD\Delete
     */
    public static function delete($table) {
        return self::createInstance('delete', $table);
    }
}