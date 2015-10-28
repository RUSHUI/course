<?php
namespace ORC\Core;
use ORC\Exception\SystemException;
use Symfony\Component\Yaml\Yaml;
use ORC\Util\AdvancedContainer;
use ORC\Core\Cache\ICacher;
use ORC\Util\Logger;
final class Config {
    
    private static $_instance;
    
    private $_config_folder;
    private $_container;
    private $_config_loaded = array();
    
    /**
     *
     * @return \ORC\Core\Config
     */
    public static function getInstance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new static();
        }
        return self::$_instance;
    }
    
    public function get($key, $default = null) {
        //first try to find whether the config is loaded
        @list($config_name,) = explode('.', $key, 2);
        $this->loadConfig($config_name);
        $data = $this->_container->get($key);
        if ($data === null) {
            $data = $default;
        }
        return $data;
    }
    
    public function exists($key) {
        @list($config_name,) = explode('.', $key, 2);
        $this->loadConfig($config_name);
        return $this->_container->exists($key);
    }
    
    public function set($key, $value) {
        @list($config_name,) = explode('.', $key, 2);
        $this->loadConfig($config_name);
        return $this->_container->set($key, $value);
    }
    /**
     * for debug only
     * @return \ORC\Util\AdvancedContainer
     */
    public function getContainer() {
        return $this->_container;
    }
    
    private function __construct() {
        if (defined('DIR_APP_CONFIG_ROOT')) {
            $this->_config_folder = DIR_APP_CONFIG_ROOT;
        } else {
            $this->_config_folder = DIR_APP_ROOT . DIRECTORY_SEPARATOR . 'config';
        }
        $this->_container = new AdvancedContainer();
        $this->initConfig();
    }
    
    private function initConfig() {
        $config_file = $this->_config_folder . DIRECTORY_SEPARATOR . 'config.yml';
        if (!file_exists($config_file)) {
            throw new SystemException('Config missing!');
        }
        $data = Yaml::parse($config_file);
        $this->_container->set('env', $data['env']);
        $this->_config_loaded[] = 'env';
    }
    
    private function getConfigFolder() {
        static $folder;
        if (!isset($folder)) {
            $folder = $this->_config_folder . DIRECTORY_SEPARATOR . $this->_container->get('env') . DIRECTORY_SEPARATOR;
        }
        return $folder;
    }
    
    private function loadConfig($config_name) {
        if (in_array($config_name, $this->_config_loaded)) {
            return;
        }
        $this->_config_loaded[] = $config_name;
        if ($config_name == 'cache') {
            //cache config will always read from yml file
            $data = false;
        } else {
            $cacher = CacheFactory::get('config_cache', 'file_config');
            $data = $cacher->getDelay($config_name);
            //$cacher = CacheFactory::get('config_cache', 'config');
            if ($data !== false && $data != ICacher::EMPTY_VALUE) {
                $this->_container->set($config_name, $data);
            }
        }
        if ($data === false) {
            //get from yml file
            $config_file = $this->getConfigFolder() . $config_name . '.yml';
            if (file_exists($config_file)) {
                $data = Yaml::parse($config_file);
                $this->_container->set($config_name, $data);
            }
            //write to cache
            if ($config_name != 'cache') {
                Logger::getInstance('config')->addNotice(sprintf('parse %s file', $config_file));
                if (empty($data)) {
                    $data = ICacher::EMPTY_VALUE;
                }
                $cacher->set($config_name, $data);
            }
        }
    }
}