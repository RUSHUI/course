<?php
namespace ORC\Core\CRUD\Callback;
use ORC\Core\Cache\ICallback;
use ORC\DAO\Table;
use ORC\DBAL\DBAL;
class GetIds extends Base implements ICallback {
    protected $_order_by;
    
    public function __construct(Table $table, $order_by = null) {
        parent::__construct($table);
        $this->_order_by = $order_by;
    }
    
	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICallback::__invoke()
     */
    public function __invoke($cacher, $key, &$value)
    {
        $expiration = $this->getCacheExpiration();
        $pk = $this->_table->getPrimaryKey();
        $dbal = DBAL::select($this->_table);
        $dbal->setSelect($pk);
        if($this->_order_by) {
            $dbal->orderBy($this->_order_by);
        }
        $value = $dbal->execute()->getByName($pk);
        if ($expiration) {
            $cacher->set($key, $value, $expiration);
            return false;
        }
        return true;
    }
}