<?php
namespace ORC\Util;
use ORC\Util\BreadCrumb\IRender;
use ORC\Util\BreadCrumb\DefaultRender;
use ORC\Exception\TemplateException;
class BreadCrumb {
    protected $_nodes = array();
    public function addNode($title, $link = null) {
        $this->_nodes[] = array('title' => $title, 'link' => $link);
    }
    
    public function getAllNodes() {
        return $this->_nodes;
    }
    
    public function render($renderer = null) {
        if ($renderer == null) {
            $render = new DefaultRender();
        } else {
            $render = new $renderer();
            if (!($render instanceof IRender)) {
                throw new TemplateException('Wrong breadcrumb render', $renderer);
            }
        }
        return $render->render($this);
    }
}