<?php
use ORC\MVC\Action;
use ORC\DBAL\DBAL;
use ORC\DAO\DaoFactory;
use ORC\Util\Pagination;
use ORC\APP\USER;
class Course_Course_Action extends Action{
    public function execute(){
        $request = $this->getRequest();
        $model = $this->getModel('Course.Course');
        switch($request->get('action')){
            case 'edit':
                return $this->HTMLView('Course.Edit');
                break;
            case 'add':
                pre($request);die();
                break;
        }
    }
}