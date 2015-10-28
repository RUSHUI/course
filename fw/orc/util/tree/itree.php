<?php
namespace ORC\Util\Tree;
interface ITree extends \Countable {
    public function __construct(INode $root);
    /**
     * 
     * @param \ORC\Util\Tree\INode $node
     * @param \ORC\Util\Tree\INode $parent
     * @return bool
     */
    public function add(INode $node, INode $parent);
    public function addByKey(INode $node, $parent_key);
    
    /**
     * 
     * @param mixed $key
     * @return \ORC\Util\Tree\INode
     */
    public function get($key);
    
    /**
     * @return \ORC\Util\Tree\INode
     */
    public function getRoot();
    /**
     * 
     * @param \ORC\Util\Tree\INode $node
     * @return bool
     */
    public function remove(INode $node);
    
    /**
     * get the sub tree
     * @param INode $node
     * @return \ORC\Util\Tree\ITree
     */
    public function getSubTree(INode $node);
    
    /**
     * get the children of a node
     * @param \ORC\Util\Tree\INode $parent null的话表示是根节点
     * @return \ORC\Util\Tree\INode[]
     */
    public function getChildren(INode $node = null);
    
    /**
     * 获得当前节点的所有后辈（子和孙等）
     * @param \ORC\Util\Tree\INode $node null表示根节点
     * @return \ORC\Util\Tree\INode[]
     */
    public function getDescendants(INode $node = null);
    /**
     * 得到当前节点的度
     * @param \ORC\Util\Tree\INode $node if set to null, root node will be used
     * @return int
     */
    public function getNodeDegree(INode $node = null);
    
    /**
     * 得到各节点度的最大值
     * @return int
     */
    public function getTreeDegree();
    
    /**
     * 某个node下面的所有叶子
     * @param \ORC\Util\Tree\INode $node
     * @return \ORC\Util\Tree\INode[]
     */
    public function getLeaves(INode $node);
    
    /**
     * get all leaves in the tree
     * @return\ORC\Util\Tree\INode[]
     */
    public function getAllLeaves();
    
    /**
     * get all brothers
     * @param \ORC\Util\Tree\INode $node
     * @param bool $withSelf 是否包含$node自己
     * @return \ORC\Util\Tree\INode[]
     */
    public function getBrothers(INode $node, $withSelf = true);
    
    /**
     * 获得某个节点的深度
     * @param \ORC\Util\Tree\INode $node
     */
    public function getDepth(INode $node);
    
    /**
     * 搜索node，如果找到则返回key，否则返回false
     * @param INode $node
     * @return mixed
     */
    public function search(INode $node);
    
    /**
     * 获得当前node的路径（从root开始）
     * @param INode $node
     */
    public function getPath(INode $node);
    
}