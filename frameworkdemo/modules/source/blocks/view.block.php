<?php
use ORC\MVC\Block;
use ORC\Application;
class Source_View_Block extends Block {
    public function render() {
        $controller = Application::getApp()->getController();
        $action = $controller->getAction();
        $view = $controller->getView();
        $r = new ReflectionObject($action);
        $action_file = $r->getFileName();
        $data = array('action_info' => array('obj' => $action, 'filename' => $action_file));
        $r = new ReflectionObject($view);
        $data['view_info'] = array('obj' => $view, 'filename' => $r->getFileName());
        return $this->renderTemplate('Source.Fragments.View', $data);
    }
}