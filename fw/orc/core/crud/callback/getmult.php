<?php
namespace ORC\Core\CRUD\Callback;
use ORC\Core\Cache\ICallbackMult;
use ORC\DAO\Table;
use ORC\DBAL\DBAL;
use ORC\Core\Cache\ICacher;
class GetMult extends Base implements ICallbackMult {
    protected $_row_classname;
    
    public function __construct(Table $table, $row_class = null) {
        parent::__construct($table);
        $this->_row_classname = $row_class;
    }
    
    /* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICallbackMult::__invoke()
     */
    public function __invoke(ICacher $cacher, array $keys, array &$values)
    {
        $expiration = $this->getCacheExpiration();
        $dbal = DBAL::select($this->_table);
        $dbal->pk($keys);
        if ($this->_row_classname) {
            $dbal->setDataRowClass($this->_row_classname);
        }
        $values = $dbal->execute()->toArray($this->_table->getPrimaryKey());
        foreach ($keys as $key) {
            if (!isset($values[$key])) {
                $values[$key] = ICacher::EMPTY_VALUE;
            }
        }
        if ($expiration) {
            $cacher->setMult($values, $expiration);
            return false;
        }
        return true;
    }
}