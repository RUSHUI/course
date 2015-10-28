<?php
use ORC\MVC\View;
class DB_List_View extends View {
    public function execute() {
        $this->renderTemplate('DB.List');
    }
}