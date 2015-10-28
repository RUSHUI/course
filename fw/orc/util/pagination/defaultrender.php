<?php
namespace ORC\Util\Pagination;
use ORC\Util\Url;
class DefaultRender extends Common implements IRender {
	/* (non-PHPdoc)
     * @see \ORC\Util\Pagination\IRender::toHtml()
     */
    public function toHtml(\ORC\Util\Pagination $pagination)
    {
        if (($totalPage = $pagination->getTotalPage()) == 0) {
            return '';
        }
        $action_name = $this->_action_name;
        $params = $this->_params;
        $output = '<nav><ul class="pagination">';
        $output .= $this->renderPreviousLink($action_name, $params, $pagination);
        if ($totalPage <= 10) {
            for($i = 1; $i <= $totalPage; $i ++) {
                $output .= $this->renderOneLink($action_name, $params, $i, $pagination);
            }
        } elseif ($pagination->getPage() < 6) {
            $output .= $this->renderOneLink($action_name, $params, 1, $pagination);
            $output .= $this->renderOneLink($action_name, $params, 2, $pagination);
            $output .= $this->renderOneLink($action_name, $params, 3, $pagination);
            $output .= $this->renderOneLink($action_name, $params, 4, $pagination);
            $output .= $this->renderOneLink($action_name, $params, 5, $pagination);
            $output .= $this->renderOneLink($action_name, $params, 6, $pagination);
            if ($pagination->getPage() == 5) {
                //再多加一个 
                $output .= $this->renderOneLink($action_name, $params, 7, $pagination);
            }
            //显示...
            $output .= $this->renderPlaceHolder();
            //显示最后两页链接
            $output .= $this->renderOneLink($action_name, $params, $totalPage - 1, $pagination);
            $output .= $this->renderOneLink($action_name, $params, $totalPage, $pagination);
        } elseif ($pagination->getPage() > ($totalPage - 5)) {
            //显示头两页链接
            $output .= $this->renderOneLink($action_name, $params, 1, $pagination);
            $output .= $this->renderOneLink($action_name, $params, 2, $pagination);
            //显示...
            $output .= $this->renderPlaceHolder();
            if ($pagination->getPage() == ($totalPage - 4)) {
                //再多加一个
                $output .= $this->renderOneLink($action_name, $params, $totalPage - 6, $pagination);
            }
            $output .= $this->renderOneLink($action_name, $params, $totalPage - 5, $pagination);
            $output .= $this->renderOneLink($action_name, $params, $totalPage - 4, $pagination);
            $output .= $this->renderOneLink($action_name, $params, $totalPage - 3, $pagination);
            $output .= $this->renderOneLink($action_name, $params, $totalPage - 2, $pagination);
            $output .= $this->renderOneLink($action_name, $params, $totalPage - 1, $pagination);
            $output .= $this->renderOneLink($action_name, $params, $totalPage, $pagination);
        } else {
            //显示头两页链接
            $output .= $this->renderOneLink($action_name, $params, 1, $pagination);
            $output .= $this->renderOneLink($action_name, $params, 2, $pagination);
            //显示...
            $output .= $this->renderPlaceHolder();
            //显示前一页，当前页和后一页
            $output .= $this->renderOneLink($action_name, $params, $pagination->getPage() - 2, $pagination);
            $output .= $this->renderOneLink($action_name, $params, $pagination->getPage() - 1, $pagination);
            $output .= $this->renderOneLink($action_name, $params, $pagination->getPage(), $pagination);
            $output .= $this->renderOneLink($action_name, $params, $pagination->getPage() + 1, $pagination);
            $output .= $this->renderOneLink($action_name, $params, $pagination->getPage() + 2, $pagination);
            //显示...
            $output .= $this->renderPlaceHolder();
            //显示最后两页链接
            $output .= $this->renderOneLink($action_name, $params, $totalPage - 1, $pagination);
            $output .= $this->renderOneLink($action_name, $params, $totalPage, $pagination);
        }
        $output .= $this->renderNextLink($action_name, $params, $pagination);
        $output .= '</ul></nav>';
        return $output;
        
    }
    
    protected function renderOneLink($action_name, array $params, $page, \ORC\Util\Pagination $pagination) {
        if ($pagination->getPage() == $page) {
            return sprintf('<li class="active"><a href="#">%d <span class="sr-only">当前页</span></a>', $page);
        } else {
            return sprintf('<li><a href="%s">%d</a></li>', Url::generateURL($action_name, $params + array('page' => $page)), $page);
        }
    }
    
    protected function renderPreviousLink($action_name, array $params, \ORC\Util\Pagination $pagination) {
        if ($pagination->getPage() == 1) {
            return sprintf('<li class="disabled"><span aria-hidden="true">&laquo;</span></li>');
        } else {
            return sprintf('<li><a href="%s" aria-label="前一页"><span aria-hidden="true">&laquo;</span></a></li>',
                Url::generateURL($action_name, $params + array('page' => ($pagination->getPage() - 1))));
        }
    }
    
    protected function renderNextLink($action_name, array $params, \ORC\Util\Pagination $pagination) {
        if ($pagination->getPage() == $pagination->getTotalPage()) {
            return sprintf('<li class="disabled"><span aria-hidden="true">&raquo;</span></li>');
        } else {
            return sprintf('<li><a href="%s" aria-label="后一页"><span aria-hidden="true">&raquo;</span></a></li>',
                Url::generateURL($action_name, $params + array('page' => ($pagination->getPage() + 1))));
        }
    }
    
    protected function renderPlaceHolder() {
        return '<li><span aria-hidden="true">···</span></li>';
    }
    
}