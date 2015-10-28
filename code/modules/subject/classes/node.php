<?php
namespace APP\Module\Subject;
use ORC\Util\Tree\INode;
use ORC\DAO\Table\DataRow;
use ORC\Exception\RuntimeException;
use ORC\DAO\Table;
class Node extends DataRow implements INode {

    /*
     * (non-PHPdoc)
     * @see \ORC\Util\Tree\INode::compare()
     */
    public function compare(\ORC\Util\Tree\INode $node)
    {
        if ($node instanceof Node) {
            return strcmp($this->get('code'), $node->get('code'));
        }
        throw new RuntimeException('不能比较两个不同类型的node');
    }

    /*
     * (non-PHPdoc)
     * @see \ORC\Util\Tree\INode::getKey()
     */
    public function getKey()
    {
        return (int)$this->get('id');
    }

    /*
     * (non-PHPdoc)
     * @see \ORC\Util\Tree\INode::getValue()
     */
    public function getValue()
    {
        return $this->getAllData();
    }

    public function __construct(DataRow $row = null)
    {
        if ($row === null) {
            $this->_data = array(
                'id' => 0,
                'name' => '请选择科目'
            );
            $this->setTable(new Table('subjects'));
        } else {
            $this->_data = $row->getAllData();
            $this->setTable($row->getTable());
        }
    }
}