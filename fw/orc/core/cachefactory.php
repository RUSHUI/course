<?php
namespace ORC\Core;
use ORC\Exception\Cache\CacheException;
use ORC\Core\Cache\MemcacheCacher;
use ORC\Core\Cache\PHPCacher;
use ORC\Core\Cache\ICacher;
use ORC\Core\Cache\FakeCacher;
use ORC\Exception\ConfigException;
class CacheFactory {
    const DEFAULT_SERVER_NAME = 'default';
    protected static $_cachers = array();
    /**
     * 
     * @param string $name
     * @param string $namespace
     * @return ICacher
     */
    public static function get($name = null, $namespace = '') {
        if(empty($name)) $name = self::DEFAULT_SERVER_NAME;
        $key = self::resolveKey($name, $namespace);
        if (!isset(self::$_cachers[$key])) {
            self::$_cachers[$key] = self::create($name, $namespace);
            self::$_cachers[$key]->connect();
        }
        return self::$_cachers[$key];
    }
    
    protected static function resolveKey($name, $namespace) {
        return md5($name . $namespace . $name);
    }
    
    protected static function create($name, $namespace) {
        $servers = Config::getInstance()->get('cache.servers');
        $config = Config::getInstance()->get('cache.' . $name);
        $server = $config['server'];
        if (isset($servers[$server])) {
            $server_info = $servers[$server];
            if (isset($config['namespace'])) {
                $server_info['namespace'] = $config['namespace'];
            }
            $namespace = self::resolveNamespace($server_info, $namespace);
            return self::createCacher($server_info, $name, $namespace);
        } else {
//             $server_info = $servers['default'];
//             if (isset($config['namespace'])) {
//                 $server_info['namespace'] = $config['namespace'];
//             }
//             $namespace = self::resolveNamespace($server_info, $namespace);
//             return self::createDefaultCacher($server_info, $name, $namespace);
            throw new ConfigException('Cache server for ' . $name . ' is missing');
        }
    }
    
    protected static function resolveNamespace(array $server_info, $namespace) {
        if (isset($server_info['namespace'])) {
            $namespace = $server_info['namespace'] . $namespace;
        }
        if (isset($server_info['prefix'])) {
            $namespace = $server_info['prefix'] . $namespace;
        }
        return $namespace;
    }
    
    protected static function createDefaultCacher($server_info, $name, $namespace) {
        if ($server_info) {
            return self::createCacher($server_info, $name, $namespace);
        }
        return self::_createPHPCacher($server_info, $name, $namespace);
    }
    
    protected static function createCacher($server_info, $name, $namespace) {
        $method_name = '_create' . $server_info['engine'] . 'cacher';
        return self::$method_name($server_info, $name, $namespace);
    }
    
    protected static function _createMemcacheCacher($server_info, $name, $namespace) {
        $cacher = new MemcacheCacher();
        foreach ($server_info['servers'] as $server) {
            list($host, $port, $weight) = explode(':', $server);
            if (empty($port)) {
                $port = 11211;
            }
            if (empty($weight)) {
                $weight = 0;
            }
            $cacher->addServer($host, $port, $weight);
        }
        if (!empty($server_info['options'])) {
            foreach ($server_info['options'] as $option => $value) {
                $cacher->setOption($option, $value);
            }
        }
        if ($namespace) {
            $cacher->setOption(ICacher::OPT_PREFIX_KEY, $namespace . '_');
        }
        return $cacher;
    }
    protected static function _createPHPCacher($server_info, $name, $namespace) {
        $cacher = new PHPCacher();
        if ($namespace) {
            $cacher->setOption(ICacher::OPT_PREFIX_KEY, $namespace . '_');
        }
        return $cacher;
    }
    protected static function _createFakeCacher($server_info, $name, $namespace) {
        $cacher = new FakeCacher();
        if ($namespace) {
            $cacher->setOption(ICacher::OPT_PREFIX_KEY, $namespace . '_');
        }
        return $cacher;
    }
    
    public function __call($name, array $arguments) {
        if (preg_match('/^_create(.*)cacher$/i', $name, $matches)) {
            throw new CacheException('Unknown cache engine', $matches[1]);
        }
        throw new CacheException('Unknown method called in CacheFactory', $name);
    }
}