<?php
use ORC\MVC\View;
class Index_Index_View extends View {
    public function execute() {
        $this->renderTemplate('Index.Index');
    }
}