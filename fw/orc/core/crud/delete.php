<?php
namespace ORC\Core\CRUD;
use ORC\DBAL\DBAL;
class Delete extends Common {
    public function del($id) {
        $pk = $this->_table->getPrimaryKey();
        $dbal = DBAL::update($this->_table);
        $dbal->pk($id);
        $result = $dbal->execute();
        $this->flushAllIdsCache();//may suffer order by field
        $this->flushItemCache($id);
        return $result;
    }
}