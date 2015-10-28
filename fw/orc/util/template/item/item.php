<?php
namespace ORC\Util\Template\Item;
interface Item {
    public function getType();
    
    public function getModuleName();
    
    public function getFilePath();
}