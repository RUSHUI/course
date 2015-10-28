<?php
namespace ORC\Util;
use ORC\Util\Storage\IStorage;
use ORC\Exception\ClassNotFoundException;
final class StorageFactory {
    private static $_instances = array();
    /**
     * 
     * @param string $engine
     * @param string $namespace
     * @return \ORC\Util\Storage\IStorage
     */
    public static function getStorage($engine, $namespace) {
        $key = sprintf('%s_%s', $engine, $namespace);
        if (!isset(static::$_instances[$key])) {
            self::$_instances[$key] = self::createStorage($engine, $namespace);
        }
        return self::$_instances[$key];
    }
    
    private static function createStorage($engine, $namespace) {
        $classname = sprintf("\\ORC\\Util\\Storage\\%sStorage", $engine);
        if (class_exists($classname)) {
            $obj = new $classname($namespace);
            if ($obj instanceof IStorage) {
                return $obj;
            }
        }
        throw new ClassNotFoundException('Engine not found!');
    }
}