<?php
//@todo try to find another better place
namespace ORC\DBAL;
use ORC\Core\CacheFactory;
use ORC\Application;
class Loader {
    const SORT_DEFAULT_ASC = '__default__ asc';
    const SORT_DEFAULT_DESC = '__default__ desc';
    
    const CACHE_PREFIX_ITEM = 'item_';
    const CACHE_PREFIX_ALL = 'all_';
    
    protected $_table;
    protected $_row_classname;
    protected $_cacher;
    
    public function __construct(\ORC\DAO\Table $table) {
        $this->_table = $table;
    }
    
    public function load(array $ids, $order_by = null) {
        $cacher = $this->getCacher();
        //first try to get from cache
        $data = $cacher->get($ids);
        $missing_ids = array_diff($ids, array_keys($data));
    }
    
    public function loadOne($id) {
        $result = $this->load(array($id));
        return isset($result[$id]) ? $result[$id] : null;
    }
    
    public function loadAll($order_by = null) {
        
    }
    
    public function flushCache($ids) {
        
    }
    
    protected function filterOrderBy($order_by) {
        return $this->_table->filterOrderBy($order_by);
    }
    
    protected function getCacher() {
        if (!isset($this->_cacher)) {
            $cacher_name = 'dbal_cache';
            $cacher_namespace = sprintf('table_%s_%s', Application::getApp()->getName(), str_replace('.', '_', $this->_table->getTableName()));
            $this->_cacher = CacheFactory::get($cacher_name, $cacher_namespace);
        }
        return $this->_cacher;
    }
}