<?php
namespace ORC\Util\Tree;
class DefaultNodeCallback implements INodeCallback {
 /* (non-PHPdoc)
     * @see \ORC\Util\Tree\INodeCallback::toString()
     */
    public function toString($depth, \ORC\Util\Tree\INode $node)
    {
        return $node->getValue();
    }

    
}