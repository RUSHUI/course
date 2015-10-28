<?php
namespace APP\Module\KnowledgePoint;
use ORC\DAO\Table\DataList;
use ORC\DAO\DaoFactory;
use ORC\DAO\Dao;
use ORC\Exception\SystemException;
use APP\Module\Subject\Subject;
use APP\Module\Exam\ExamType;
use ORC\DAO\Util;
class KnowledgePoints {
    /**
     * @return \ORC\DAO\Table\DataList
     */
    public function getAll() {
        static $points;
        if ($points == null) {
            $points = Util::getTableData('knowledge_points');
        }
        return $points;
    }
    
    /**
     * 
     * @param array $criteria
     * @return \APP\Module\KnowledgePoint\DataRow\KnowledgePoint[]
     */
    public function search(array $criteria) {
        $points = $this->getAll();
        $result = array();
        foreach ($points as $point) {
            $find = true;
            foreach ($criteria as $field => $value) {
                if ($point->get($field) != $value) {
                    $find = false;
                }
            }
            if ($find) {
                $result[$point->get('id')] = $point;
            }
        }
        return $result;
    }
    
    /**
     * 将科目、子科目、考试项目等放入知识点中
     * @param DataList $list
     * @return void $list已经改变，不需要返回值
     */
    public function loadExtra(DataList $list) {
        $subjectLoader = new Subject();
        $subjects = $subjectLoader->getAllSubjects();
        $examTypeLoader = new ExamType();
        $examTypes = $examTypeLoader->getAllTypes();
        foreach ($list as $row) {
            $row->set('subject', $subjects[$row->get('subject_id')]);
            $row->set('primary_exams', $examTypes[$row->get('primary_exam_type')]);
            $secondary_ids = $row->getSecondaryExamTypeIds();
            $temp = array();
            foreach ($secondary_ids as $id) {
                $temp[$id] = $examTypes[$id];
            }
            $row->set('secondary_exams', $temp);
        }
    }
    
    /**
     * 获得新的知识点的编号
     * @param int $exam_type_id
     * @param int $subject_id
     * @return string编号
     */
    public function createNewCode($exam_type_id, $subject_id) {
//         $dbal = DBAL::insert('knowledge_point_id');
//         $dbal->set('primary_exam_type', $exam_type_id);
//         $dbal->set('subject_id', $subject_id);
//         $dbal->set('id', '@COUNT:=(1)');
//         $dbal->setRawDuplicate('id = @COUNT:=(id + 1)');
//         $dbal->execute();
//         $dao = $dbal->getDao();

        $subjectLoader = new Subject();
        $subjects = $subjectLoader->getAllSubjects();
        if (!isset($subjects[$subject_id])) {
            return false;
        }
        $subject_code = $subjects[$subject_id]['code'];
        $examTypeLoader = new ExamType();
        $types = $examTypeLoader->getAllTypes();
        if (!isset($types[$exam_type_id])) {
            return false;
        }
        $exam_type_code = $types[$exam_type_id]['code'];
        
        $dao = DaoFactory::get();
        $dao->query("INSERT INTO knowledge_point_id (primary_exam_type, subject_id, id) VALUES ($exam_type_id, $subject_id, @COUNT:=(1)) ON DUPLICATE KEY UPDATE id = @COUNT:=(id + 1)");
        $dao->query('SELECT @COUNT');
        $data = $dao->fetch(Dao::FETCH_NUM);
        $id = $data[0];
        if ($id < 1) {
            throw new SystemException('生成知识点编号失败', $dao);
        }
        return sprintf('%s-%s-ZSD-%d', $exam_type_code, $subject_code, $id);
    }
    
}