<?php
use ORC\MVC\Action;
class Index_Index_Action extends Action {
    public function execute() {
        return $this->HTMLView('Index.Index');
    }
}