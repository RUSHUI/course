<?php
namespace APP\Module\Exam;
use ORC\Core\CRUD;
class ExamType {
    public function getAllTypes() {
        static $types;
        if (!isset($types)) {
            $crud = CRUD::read('exam_types');
            $list = $crud->getAll();
            $types = array();
            foreach ($list as $type) {
                $types[$type['id']] = $type->getAllData();
            }
        }
        return $types;
    }
}