<?php
namespace ORC\Util\Tree;
use ORC\Exception\RuntimeException;
class UnSortedTree implements ITree {
    /**
     * 所有数据
     * @var INode[]
     */
    protected $data = array();
    protected $parentKeys = array();
    protected $leaveKeys = array();
    protected $childKeys = array();
    protected $depth = array();
    protected $orphan = array();
    protected $root;
    /* (non-PHPdoc)
     * @see \ORC\Util\Tree\ITree::getDepth()
     */
    public function getDepth(\ORC\Util\Tree\INode $node)
    {
        if (array_key_exists($node->getKey(), $this->depth)) {
            return $this->depth[$node->getKey()];
        } else {
            if (!isset($this->parentKeys[$node->getKey()])) {
                throw new RuntimeException('存在无父孤儿节点！');
            }
            $parentKey = $this->parentKeys[$node->getKey()];
            $depth = 1;
            while ($parentKey != $this->root->getKey()) {
                if (isset($this->data[$parentKey])) {
                    $n = $this->data[$parentKey];
                    $parentKey = $this->parentKeys[$n->getKey()];
                    $depth ++;
                } else {
                    throw new RuntimeException('存在无父孤儿节点！');
                }
            }
            return $this->depth[$node->getKey()] = $depth;
        }
    }

 /* (non-PHPdoc)
     * @see Countable::count()
     */
    public function count()
    {
        return count($this->data);
    }

 /* (non-PHPdoc)
     * @see \ORC\Util\Tree\ITree::get()
     */
    public function get($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }
    
    public function getRoot() {
        return $this->root;
    }

 /* (non-PHPdoc)
     * @see \ORC\Util\Tree\ITree::__construct()
     */
    public function __construct(\ORC\Util\Tree\INode $root) {
        $this->addToData($root);
        $this->root = $root;
        $this->depth[$root->getKey()] = 0;
    }
    
    /* (non-PHPdoc)
     * @see \ORC\Util\Tree\ITree::add()
     */
    public function add(\ORC\Util\Tree\INode $node, \ORC\Util\Tree\INode $parent)
    {
        $parentKey = $parent->getKey();
        return $this->addByKey($node, $parentKey);
    }
    
    public function addByKey(\ORC\Util\Tree\INode $node, $parentKey) {
        $this->addToData($node);
        if (!isset($this->childKeys[$parentKey])) {
            $this->childKeys[$parentKey] = array();
        }
        if (array_search($node->getKey(), $this->childKeys) !== false) {
            //node已经存在
            return false;
        }
        $this->childKeys[$parentKey][] = $node->getKey();
        $this->parentKeys[$node->getKey()] = $parentKey;
        //检测leaves
        if (($index = array_search($parentKey, $this->leaveKeys)) !== false) {
            //有子节点则从叶子中去掉
            unset($this->leaveKeys[$index]);
        }
        $this->leaveKeys[] = $node->getKey();
        //深度
        if (isset($this->depth[$parentKey])) {
            $this->depth[$node->getKey()] = $this->depth[$parentKey] + 1;
        }
        //检查是否parent不存在
        if (!isset($this->data[$parentKey])) {
            $this->orphan[$parentKey] = null;
        }
        return true;
    }

 /* (non-PHPdoc)
     * @see \ORC\Util\Tree\ITree::getAllLeaves()
     */
    public function getAllLeaves()
    {
        return $this->getNodes($this->leaveKeys);
    }

 /* (non-PHPdoc)
     * @see \ORC\Util\Tree\ITree::getBrothers()
     */
    public function getBrothers(\ORC\Util\Tree\INode $node, $withSelf = true)
    {
        if (!isset($this->parentKeys[$node->getKey()])) {
            throw new RuntimeException('节点在树中不存在');
        }
        if ($node->getKey() == $this->root->getKey()) {
            if ($withSelf) {
                return array($this->root->getKey() => $this->root);
            } else {
                return array();
            }
        }
        $parentKey = $this->parentKeys[$node->getKey()];
        if ($withSelf) {
            return $this->getNodes($this->childKeys[$parentKey]);
        } else {
            $brotherKeys = $this->childKeys[$parentKey];
            $index = array_search($node->getKey(), $brotherKeys);
            unset($brotherKeys[$index]);
            return $this->getNodes($brotherKeys);
        }
    }
    /* (non-PHPdoc)
     * @see \ORC\Util\Tree\ITree::getChildren()
     */
    public function getChildren(\ORC\Util\Tree\INode $node = null)
    {
        if ($node == null) {
            $node = $this->root;
        }
        if (isset($this->childKeys[$node->getKey()])) {
            return $this->getNodes($this->childKeys[$node->getKey()]);
        }
        return array();
    }
    
 /* (non-PHPdoc)
     * @see \ORC\Util\Tree\ITree::getDescendants()
     */
    public function getDescendants(\ORC\Util\Tree\INode $node = null)
    {
        if ($node == null) {
            $node = $this->root;
        }
        $childrenKeys = $this->getAllChildrenKeys($node->getKey());
        return $this->getNodes($childrenKeys);
    }

 /* (non-PHPdoc)
     * @see \ORC\Util\Tree\ITree::getLeaves()
     */
    public function getLeaves(\ORC\Util\Tree\INode $node)
    {
        $childrenKeys = $this->getAllChildrenKeys($node->getKey());
        $leaveKeys = array_intersect($childrenKeys, $this->leaveKeys);
        return $this->getNodes($leaveKeys);
    }

 /* (non-PHPdoc)
     * @see \ORC\Util\Tree\ITree::getNodeDegree()
     */
    public function getNodeDegree(\ORC\Util\Tree\INode $node = null)
    {
        if ($node == null) {
            $node = $this->root;
        }
        $key = $node->getKey();
        if (!isset($this->childKeys[$key])) {
            return 0;
        }
        return count($this->childKeys[$key]);
    }

 /* (non-PHPdoc)
     * @see \ORC\Util\Tree\ITree::getSubTree()
     */
    public function getSubTree(\ORC\Util\Tree\INode $node)
    {
        $tree = new self($node);
        $childrenKeys = $this->getAllChildrenKeys($node->getKey());
        foreach ($childrenKeys as $key) {
            $child = $this->get($key);
            $tree->addByKey($child, $this->parentKeys[$key]);
        }
        return $tree;
    }

 /* (non-PHPdoc)
     * @see \ORC\Util\Tree\ITree::getTreeDegree()
     */
    public function getTreeDegree()
    {
        $maxDegree = 0;
        $all_keys = array_keys($this->childKeys);
        foreach ($all_keys as $key) {
            $degree = count($this->childKeys[$key]);
            if ($degree > $maxDegree) {
                $maxDegree = $degree;
            }
        }
        return $maxDegree;
    }

 /* (non-PHPdoc)
     * @see \ORC\Util\Tree\ITree::remove()
     */
    public function remove(\ORC\Util\Tree\INode $node)
    {
        //first get all childrens
        $keys = $this->getAllChildrenKeys($node->getKey());
        $keys[] = $node->getKey();
        //remove all
        foreach ($keys as $key) {
            unset($this->data[$key]);
            unset($this->parentKeys[$key]);
        }
        $this->parentKeys = array_diff($this->parentKeys, $keys);
        $this->leaveKeys = array_diff($this->leaveKeys, $keys);
    }
    /* (non-PHPdoc)
     * @see \ORC\Util\Tree\ITree::search()
     */
    public function search(\ORC\Util\Tree\INode $node) {
        foreach ($this->data as $key => $value) {
            if ($node->compare($value) == 0) {
                return $key;
            }
        }
        return false;
    }
    
    public function getPath(\ORC\Util\Tree\INode $node) {
        $parentKey = $this->parentKeys[$node->getKey()];
        $pathes = array($node);
        while ($parentKey != $this->root->getKey()) {
            $node = $this->get($parentKey);
            $pathes[] = $node;
            $parentKey = $this->parentKeys[$node->getKey()];
        }
        $pathes[] = $this->root;
        return array_reverse($pathes);
    }
    
    public function getOrphanKeys() {
        return array_keys($this->orphan);
    }
    
    public function getOrphan() {
        $keys = array_keys($this->orphan);
        $childrenKeys = array();
        foreach ($keys as $key) {
            if (isset($this->childKeys[$key])) {
                $childrenKeys = array_merge($childrenKeys, $this->childKeys[$key]);
            }
        }
        return $this->getNodes($childrenKeys);
    }
    
    protected function addToData(\ORC\Util\Tree\INode $node, $strict = true) {
        if (!isset($this->data[$node->getKey()])) {
            $this->data[$node->getKey()] = $node;
            unset($this->orphan[$node->getKey()]);
        } elseif ($strict) {
            throw new RuntimeException('数据已经存在，不能重复添加');
        }
    }
    
    protected function getAllChildrenKeys($parentKey) {
        if (isset($this->childKeys[$parentKey])) {
            $childrenKeys = $this->childKeys[$parentKey];
            foreach ($childrenKeys as $key) {
                $childrenKeys = array_merge($childrenKeys, $this->getAllChildrenKeys($key));
            }
            return $childrenKeys;
        }
        return array();
    }
    
    protected function getNodes(array $keys) {
        $result = array();
        foreach ($keys as $key) {
            if (isset($this->data[$key])) {
                $result[$key] = $this->data[$key];
            } else {
                //debug only
//                 echo 'unknown key';
            }
        }
        return $result;
    }
}