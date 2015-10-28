<?php
namespace ORC\Util;
use ORC\Exception\SystemException;
/**
 * auto generate files
 * @author 彦钦
 *
 */
class AutoGen {
    private static $_generators;
    private function __construct() {
        
    }
    
    /**
     * 
     * @param string $name
     * @throws SystemException
     * @return \ORC\Util\AutoGen\Generator
     */
    public static function getGenerator($name) {
        $name = str_replace(array('-', '_'), '', $name);
        if (isset(self::$_generators[$name])) {
            return self::$_generators[$name];
        }
        $class_name = "\\ORC\\Util\\AutoGen\\" . $name . 'Generator';
        if (class_exists($class_name)) {
            $generator = new $class_name($name);
            if (!($generator instanceof \ORC\Util\AutoGen\Generator)) {
                throw new SystemException('unknown generator');
            }
        } else {
            $generator = new \ORC\Util\AutoGen\Generator($name);
        }
        self::$_generators[$name] = $generator;
        return $generator;
    }
    
    public static function getFileCheckSum($file) {
        return md5(file_get_contents($file));
    }
}