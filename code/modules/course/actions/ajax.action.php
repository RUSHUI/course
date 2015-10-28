<?php
use ORC\MVC\Action;
use ORC\DBAL\DBAL;
use ORC\DAO\DaoFactory;
use ORC\Util\Pagination;
use ORC\APP\USER;
class Admin_Ajax_Action extends Action{
    public function execute(){
        $request = $this->getRequest();
        $model = $this->getModel('Admin.Ajax');
        switch($request->get('action')){
            case 'edit':
                echo 11111;die();
                break;
        }
    }
}