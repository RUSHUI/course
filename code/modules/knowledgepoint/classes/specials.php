<?php
namespace APP\Module\KnowledgePoint;
use ORC\Core\CRUD;
class Specials {
    public function getAll() {
        $crud = CRUD::read('specials');
        return $crud->getAll();
    }
}