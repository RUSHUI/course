<?php
namespace ORC\Util\Storage;
use ORC\Core\CacheFactory;
class MemStorage implements IStorage {
    protected $_cacher;
    protected $_namespace;
    public function __construct($namespace = '') {
        $this->_namespace = sprintf('__storage__%s', $namespace);
        $this->_cacher = CacheFactory::get('memcache', $this->_namespace);
    }
    /* (non-PHPdoc)
     * @see \ORC\Util\Storage\IStorage::get()
     */
    public function get($key)
    {
        return $this->_cacher->get($key);
    }
    
    /* (non-PHPdoc)
     * @see \ORC\Util\Storage\IStorage::set()
     */
    public function set($key, $value)
    {
        return $this->_cacher->set($key, $value);
    }
}