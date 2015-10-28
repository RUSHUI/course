<?php
namespace ORC\Util\BreadCrumb;
use ORC\Util\BreadCrumb;
interface IRender {
    /**
     * 
     * @param BreadCrumb $breadcrumb
     * @return the html content
     */
    public function render(BreadCrumb $breadcrumb);
}