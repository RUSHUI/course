<?php
namespace ORC\Core\CRUD;
use ORC\Core\CRUD;
use ORC\Core\Cache\ICacher;
use ORC\Core\CRUD\Callback\GetMult;
use ORC\Core\CRUD\Callback\GetIds;
use ORC\DAO\Table\DataList;
class Read extends Common {

    public function get($id) {
        $data = $this->getMult(array($id));
        return isset($data[$id]) ? $data[$id] : null;
    }
    
    /**
     * 
     * @param array $ids
     * @param string $toDataList
     * @return Ambigous <\ORC\DAO\Table\DataList, \ORC\DAO\Table\DataRow[] >
     */
    public function getMult(array $ids, $toDataList = false) {
        $data = $this->getCacher(CRUD::CACHE_PREFIX_ITEM)->getMult($ids, new GetMult($this->_table, $this->_row_classname));
        //         pre($data);
        //strip the empty value and maintain the order
        $result = array();
        foreach ($ids as $id) {
            if ($data[$id] !== ICacher::EMPTY_VALUE) {
                $result[$id] = $data[$id];
            }
        }
        //         pre($result);
        if ($toDataList) {
            $result = new DataList($result, $this->_table);
        }
        return $result;
    }
    
    /**
     * 
     * @param string $order_by
     * @param bool $toDataList 如果是true则返回DataList，否则返回数组
     * @return Ambigous <\ORC\DAO\Table\DataList, \ORC\DAO\Table\DataRow[] >
     */
    public function getAll($order_by = null, $toDataList = false) {
        $ids = $this->getAllIds($order_by);
        return $this->getMult($ids, $toDataList);
    }
    
    public function getAllIds($order_by = null) {
        $order_by = $this->filterOrderBy($order_by);
        if ($order_by) {
            $cache_key = sprintf('ids_%s', str_replace(' ', '_', $order_by));
        } else {
            $cache_key = 'ids';
        }
//         pre($this->getCacher(CRUD::CACHE_PREFIX_ALL)->getAllKeys());
        return $this->getCacher(CRUD::CACHE_PREFIX_ALL)->get($cache_key, new GetIds($this->_table, $order_by));
    }
}