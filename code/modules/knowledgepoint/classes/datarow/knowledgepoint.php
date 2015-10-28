<?php
namespace APP\Module\KnowledgePoint\DataRow;
use ORC\DAO\Table\DataRow;
class KnowledgePoint extends DataRow {
    public function getSecondaryExamTypeIds() {
        if ($this->exists('secondary_exam_type_ids')) {
            return $this->get('secondary_exam_type_ids');
        }
        $ids = $this->get('secondary_exam_types');
        $ids = trim($ids, '|');
        return $this->set('secondary_exam_type_ids', array_filter(explode('|', $ids)));
    }
}