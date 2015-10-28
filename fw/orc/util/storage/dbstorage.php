<?php
namespace ORC\Util\Storage;
class DBStorage implements IStorage {
    public function __construct($namespace = '') {
        //判断表是否存在，不存在要创建表并创建yml文件
        //TODO
    }
    /* (non-PHPdoc)
     * @see \ORC\Util\Storage\IStorage::get()
     */
    public function get($key)
    {
        // TODO Auto-generated method stub
    
    }
    
    /* (non-PHPdoc)
     * @see \ORC\Util\Storage\IStorage::set()
     */
    public function set($key, $value)
    {
        // TODO Auto-generated method stub
    
    }
}