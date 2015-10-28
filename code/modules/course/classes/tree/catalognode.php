<?php
namespace APP\Module\Course\Tree;
use ORC\Util\Tree\INode;
use ORC\DAO\Table\DataRow;
class CatalogNode implements INode {
    private $datarow;
    public function setDataRow(DataRow $row) {
        $this->datarow = $row;
    }
 /* (non-PHPdoc)
     * @see \ORC\Util\Tree\INode::compare()
     */
    public function compare(\ORC\Util\Tree\INode $node)
    {
        // TODO Auto-generated method stub
        
    }

 /* (non-PHPdoc)
     * @see \ORC\Util\Tree\INode::getKey()
     */
    public function getKey()
    {
        // TODO Auto-generated method stub
        return $this->getDataRow()->get('id');
    }

 /* (non-PHPdoc)
     * @see \ORC\Util\Tree\INode::getValue()
     */
    public function getValue()
    {
        // TODO Auto-generated method stub
        return $this->datarow;
    }

    
}