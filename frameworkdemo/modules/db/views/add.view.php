<?php
use ORC\MVC\View;
class DB_Add_View extends View {
    public function execute() {
        $this->renderTemplate('DB.Form');
    }
}