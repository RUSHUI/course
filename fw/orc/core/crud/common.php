<?php
namespace ORC\Core\CRUD;
use ORC\DAO\Table;
use ORC\Exception\SystemException;
use ORC\Application;
use ORC\Core\Config;
use ORC\Core\CacheFactory;
use ORC\Core\Cache\ICacher;
use ORC\Core\CRUD;
abstract class Common {
    protected $_table;
    protected $_row_classname;
    protected $_cachers;
    
    public function __construct($table) {
        if (is_string($table)) {
            $table = new Table($table);
        }
        if (!($table instanceof Table)) {
            throw new SystemException('wrong param for CRUD', $table);
        }
        $this->_table = $table;
    }
    
    public function setRowClass($class_name) {
        $this->_row_classname = $class_name;
    }
    
    public function flushItemCache($id) {
        return $this->flushMultItemCache(array($id));
    }
    
    public function flushMultItemCache(array $ids) {
        return $this->getCacher(CRUD::CACHE_PREFIX_ITEM)->delete($ids);
    }
    
    public function flushAllIdsCache() {
        return $this->getCacher(CRUD::CACHE_PREFIX_ALL)->deleteAll();
    }
    
    protected function filterOrderBy($order_by) {
        return $this->_table->filterOrderBy($order_by);
    }
    
    /**
     * 
     * @param string $namespace
     * @return ICacher
     */
    protected function getCacher($namespace) {
        if (!isset($this->_cachers[$namespace])) {
            $config = Config::getInstance();
            $server_name = $config->get('crud.' . $this->_table->getServerName() . '.' . $this->_table->getTableName() . '.cache.server');
            if (!$server_name) {
                $server_name = 'default';
            }
            //make sure the namespace is unique
            $cacher_namespace = $this->resloveNamespace($namespace);
            $this->_cachers[$namespace] = CacheFactory::get($server_name, $cacher_namespace);
        }
        return $this->_cachers[$namespace];
    }
    
    protected function resloveNamespace($namespace) {
        return sprintf('crud_table_%s_%s_%s', Application::getApp()->getName(), str_replace('.', '_', $this->_table->getFullTableName()), $namespace);
    }
}