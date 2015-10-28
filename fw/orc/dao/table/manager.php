<?php
namespace ORC\DAO\Table;
use ORC\DAO\Table;
use ORC\DAO\Util;
use ORC\Exception\SystemException;
use ORC\DBAL\DBAL;
class Manager {
    private $table_name;
    private $data;
    
    /**
     * 
     * @param string/\ORC\DAO\Table $table_name
     */
    public function __construct($table_name) {
        if ($table_name instanceof Table) {
            $table_name = $table_name->getTableName();
        }
        $this->table_name = $table_name;
    }
    
    /**
     * 获取表中所有数据
     * @return \ORC\DAO\Table\DataList
     */
    public function getAll() {
        if (!isset($this->data)) {
            $this->data = Util::getTableData($this->table_name);
        }
        return $this->data;
    }
    
    /**
     * 保存数据（根据主键自动选择更新或者插入）
     * @param array $data
     * @throws SystemException
     * @return Ambigous <\ORC\DBAL\Common\int/string, boolean>
     */
    public function save(array $data) {
        $table = new Table($this->table_name);
        $pk = $table->getPrimaryKey();
        if ($pk) {
            $pks = explode(',', $pk);
        } else {
            $pks = array();
        }
        $is_new = true;
        $pk_values = array();
        $values = array();
        foreach ($data as $key => $value) {
            if (in_array($key, $pks)) {
                //说明有主键值，是更新
                $is_new = false;
                $pk_values[$key] = $value;
            } else {
                $values[$key] = $value;
            }
        }
        if ($is_new == false) {
            if (count($pks) != count($pk_values)) {
                throw new SystemException('主键数量不匹配');
            }
            //如果是指定pk值但是是新建
            $is_update = false;
            $data = $this->getAll();
            //比较pk vlaue
            foreach ($data as $row) {
                $found = true;
                foreach ($pk_values as $k => $v) {
                    if ($row->get($k) != $v) {
                        $found = false;
                        break;
                    }
                }
                if ($found) {
                    $is_update = true;
                    break;
                }
            }
            if ($is_update == false) {
                $is_new = true;//说明虽然指定了主键值，但是数据库里找不到对应的值，所以仍然是插入
            }
        }
        if ($is_new) {
            $dbal = DBAL::insert($this->table_name);
        } else {
            $dbal = DBAL::update($this->table_name);
            foreach ($pk_values as $key => $value) {
                $dbal->findBy($key, array($value));
            }
        }
        foreach ($values as $key => $value) {
            $dbal->set($key, $value);
        }
        $result = $dbal->execute();
        $this->flushCache();
        return $result;
    }
    protected function flushCache() {
        Util::flushTableDataCache($this->table_name);
        unset($this->data);
        return true;
    }
}