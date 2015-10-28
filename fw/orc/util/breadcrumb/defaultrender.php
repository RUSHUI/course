<?php
namespace ORC\Util\BreadCrumb;
class DefaultRender implements IRender {
	/* (non-PHPdoc)
     * @see \ORC\Util\BreadCrumb\IRender::render()
     */
    public function render(\ORC\Util\BreadCrumb $breadcrumb)
    {
        $nodes = $breadcrumb->getAllNodes();
        if (count($nodes) == 0) {
            return '';
        }
        $outputs = array();
        foreach ($nodes as $node) {
            if ($node['link']) {
                $outputs[] = sprintf('<a href="%s">%s</a>', $node['link'], $node['title']);
            } else {
                $outputs[] = $node['title'];
            }
        }
        return implode('&nbsp;&gt;&nbsp;', $outputs);
    }

    
}