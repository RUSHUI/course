<?php
namespace APP\Module\Subject;
use ORC\Core\CRUD;
use ORC\Util\Tree\Util;
class Subject {
    protected $tree;
    /**
     * 
     * @return \ORC\Util\Tree\UnSortedTree
     */
    public function getTree() {
        if (!isset($this->tree)) {
            $this->tree = Util::createFromDB('subjects', '\APP\Module\Subject\Node', false);
        }
        return $this->tree;
    }
    
    /**
     * 取得第一级子科目
     * @return \ORC\Util\Tree\INode[]
     */
    public function getAllSubjects($toArray = false) {
        $tree = $this->getTree();
        $subjects = $tree->getChildren();
        if ($toArray) {
            $result = array();
            foreach ($subjects as $subject) {
                $result[$subject->get('id')] = $subject->getAllData();
            }
            return $result;
        }
        return $subjects;
    }
    
    /**
     * 取得第二级子科目
     * @param string $toArray
     * @return \ORC\Util\Tree\INode[]
     */
    public function getSubSubjects($extend = true, $toArray = false) {
        $tree = $this->getTree();
        $subjects = $tree->getChildren();
        $result = array();
        foreach ($subjects as $subject) {
            $nodes = $tree->getChildren($subject);
            foreach ($nodes as $node) {
                $node->set('subject_id', $subject->get('id'));
                if ($extend) {
                    if ($toArray) {
                        $node->set('subject', $subject->getAllData());
                    } else {
                        $node->set('subject', $subject);
                    }
                }
                if ($toArray) {
                    $result[$node->get('id')] = $node->getAllData();
                } else {
                    $result[$node->get('id')] = $node;
                }
            }
        }
        return $result;
    }
    /**
     * 取得最后一级子科目
     * @param string $extend
     * @return \ORC\Util\Tree\INode[]
     */
    public function getLatestSubjects($extend = true, $toArray = false) {
        $tree = $this->getTree();
        $subjects = $tree->getChildren();
        $result = array();
        foreach ($subjects as $node) {
            $leaves = $tree->getLeaves($node);
            foreach ($leaves as $leaf) {
                $leaf->set('subject_id', $node->get('id'));
                if ($extend) {
                    if ($toArray) {
                        $leaf->set('subject', $node->getAllData());
                    } else {
                        $leaf->set('subject', $node);
                    }
                }
                if ($toArray) {
                    $result[$leaf->get('id')] = $leaf->getAllData();
                } else {
                    $result[$leaf->get('id')] = $leaf;
                }
            }
        }
        return $result;
    }
}


class Subject_old {
    protected $subjects;
    protected $sub_subjects;
    
    public function getAllSubjects() {
        if (!isset($this->subjects)) {
            $crud = CRUD::read('subjects');
            $list = $crud->getAll();
            $this->subjects = array();
            foreach ($list as $id => $subject) {
                $this->subjects[$id] = $subject->getAllData();
            }
        }
        return $this->subjects;
    }
    
    public function getAllSubSubjects($extend = true) {
        if (!isset($this->sub_subjects)) {
            if ($extend) {
                $subjects = $this->getAllSubjects();
            }
            $crud = CRUD::read('sub_subjects');
            $list = $crud->getAll();
            $this->sub_subjects = array();
            foreach ($list as $id => $sub_subject) {
                $sub = $sub_subject->getAllData();
                if ($extend) {
                    $sub['subject'] = $subjects[$sub['subject_id']];
                }
                $this->sub_subjects[$sub['id']] = $sub;
            }
        }
        return $this->sub_subjects;
    }
}