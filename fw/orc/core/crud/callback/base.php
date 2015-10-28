<?php
namespace ORC\Core\CRUD\Callback;
use ORC\Core\Config;
use ORC\DAO\Table;
abstract class Base {
    protected $_table;
    
    public function __construct(Table $table) {
        $this->_table = $table;
    }
    
    protected function getCacheExpiration() {
        $config = Config::getInstance();
        $expiration = $config->get('crud.' . $this->_table->getTableName() . '.cache.expire');
    }
}