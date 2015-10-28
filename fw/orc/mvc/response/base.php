<?php
namespace ORC\MVC\Response;
abstract class Base {
    protected $_controller;
    public function __construct(\ORC\MVC\Controller $controller) {
        $this->_controller = $controller;
    }
    abstract public function render();
    
}