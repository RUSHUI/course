<?php
namespace ORC\MVC\Response;
class RAW extends Base {

    public function render()
    {
        $content = $this->_controller->getResponse()->getContent();
        return $content;
    }
}