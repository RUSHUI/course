<?php
namespace ORC\Core\CRUD;
use ORC\DBAL\DBAL;
use ORC\Exception\SystemException;
class Update extends Common {
    public function save(array $item) {
        $pk = $this->_table->getPrimaryKey();
        if (!isset($item[$pk])) {
            throw new SystemException('Primary Key needed for CRUD::Update');
        }
        $dbal = DBAL::update($this->_table);
        $dbal->pk($item[$pk]);
        foreach ($item as $key => $value) {
            if ($key == $pk) continue;
            $dbal->set($key, $value);
        }
        $result = $dbal->execute();
        $this->flushAllIdsCache();//may suffer order by field
        $this->flushItemCache($item[$pk]);
        return $result;
    }
}