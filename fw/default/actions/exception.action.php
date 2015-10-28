<?php
use ORC\MVC\Action;
class Default_Exception_Action extends Action {
    public function execute() {
        $ex = $this->getRequest()->get('exception');
        pre($ex);
        exit();
    }
}