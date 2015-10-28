<?php
namespace ORC\Util\Tree;
interface INode {
    /**
     * 比较两个node
     * @param INode $node
     * @return int 相同返回0， 当前node比$node小返回负数
     */
    public function compare(INode $node);
    
    /**
     * get the unique key
     * @return int|string
     */
    public function getKey();
    
    /**
     * @return mixed
     */
    public function getValue();
}