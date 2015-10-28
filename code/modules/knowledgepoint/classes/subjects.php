<?php
namespace APP\Module\KnowledgePoint;
use ORC\Core\CRUD;
class Subjects {
    public function getAll() {
        $crud = CRUD::read('subjects');
        return $crud->getAll();
    }
}