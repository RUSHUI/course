<?php
namespace ORC\Util\Tree;
use ORC\DAO\Table;
class Util {
    /**
     * 深度优先遍历树
     * @param ITree $tree
     * @param INodeCallback $nodeCallback
     * @return INode[]
     */
    public static function depthFirstTraversal(ITree $tree, INodeCallback $nodeCallback = null) {
        $result = array();
        $root = $tree->getRoot();
        if ($nodeCallback == null) {
            $nodeCallback = new DefaultNodeCallback();
        }
        $result[$root->getKey()] = $nodeCallback->toString(0, $root);
        
        $children = $tree->getChildren($root);
        foreach ($children as $child) {
            $result[$child->getKey()] = $nodeCallback->toString(1, $child);
            $result += self::_dft($tree, $child, 2, $nodeCallback);
        }
        return $result;
    }
    
    protected static function _dft(ITree $tree, INode $parent, $depth, INodeCallback $nodeCallback) {
        $children = $tree->getChildren($parent);
        $result = array();
        foreach ($children as $child) {
            $result[$child->getKey()] = $nodeCallback->toString($depth, $child);
            $result += self::_dft($tree, $child, $depth + 1, $nodeCallback);
        }
        return $result;
    }
    
    /**
     * 广度优先遍历树
     * @param ITree $tree
     * @param INodeCallback $nodeCallback
     * @return INode[]
     */
    public static function breadthFirstTraversal(ITree $tree, INodeCallback $nodeCallback = null) {
        $result = array();
        $root = $tree->getRoot();
        if ($nodeCallback == null) {
            $nodeCallback = new DefaultNodeCallback();
        }
        $result[$root->getKey()] = $nodeCallback->toString(0, $root);
        $children = $tree->getChildren($root);
        while (count($children) > 0) {
            $tmp = array();
            foreach ($children as $child) {
                $result[$child->getKey()] = $nodeCallback->toString(1, $child);
                $tmp += $tree->getChildren($child);
            }
            $children = $tmp;
        }
        return $result;
    }
    
    public static function createFromDB($table, $nodeClass, $useCache = true) {
        if ($table instanceof Table) {
            $table_name = $table->getTableName();
        }
        $list = \ORC\DAO\Util::getTableData($table, $useCache);
        $tree = new UnSortedTree(new $nodeClass());
        foreach ($list as $row) {
            $tree->addByKey(new $nodeClass($row), $row->get('parent_id'));
        }
        return $tree;
    }
}