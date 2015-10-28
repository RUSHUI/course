<?php
namespace ORC\Core\CRUD;
use ORC\Exception\SystemException;
use ORC\DBAL\DBAL;
class Create extends Common {
    public function save(array $item) {
        $pk = $this->_table->getPrimaryKey();
        if (isset($item[$pk])) {
            throw new SystemException('Duplicate Primary Key set for CRUD::Create');
        }
        $dbal = DBAL::insert($this->_table);
        foreach ($item as $key => $value) {
            $dbal->set($key, $value);
        }
        $id = $dbal->execute();
        $this->flushAllIdsCache();
        return $id;
    }
}