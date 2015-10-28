<?php
namespace ORC\Util\Tree;
interface INodeCallback {
    public function toString($depth, INode $node);
}