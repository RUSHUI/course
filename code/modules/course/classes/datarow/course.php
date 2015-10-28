<?php
namespace APP\Module\Course\DataRow;
use ORC\DAO\Table\DataRow;
use APP\Module\Subject\Subject;
use ORC\Exception\RuntimeException;
use ORC\DBAL\DBAL;
use ORC\Util\Tree\UnSortedTree;
use APP\Module\Course\Tree\CatalogNode;
/**
 * 对应course表
 * @author pal
 *
 */
class Course extends DataRow {
    private $subject;
    private $catalogs;
    private $video;
    private $materials;
    
    private function getSubject() {
        if (!isset($this->subject)) {
            $subject_id = $this->get('subject_id');
            $subjectLoader = new Subject();
            $this->subject = $subjectLoader->getTree()->get($subject_id);
            if (!($this->subject instanceof \APP\Module\Subject\Node)) {
                throw new RuntimeException('不正确的科目id');
            }
        }
        return $this->subject;
    }
    public function getId() {
        return $this->get('id');
    }
    /**
     * 获得当前科目名称
     */
    public function getSubjectName() {
        return $this->getSubject()->get('name');
    }
    
    /**
     * 获得所有的课程目录
     * 每个datarow对象必须是\APP\Module\Course\DataRow\CourseCatalog
     * @return \ORC\DAO\Table\DataList
     */
    public function getCatalogs() {
        if (!isset($this->catalogs)) {
            $dbal = DBAL::select('course_catalogs');
            $dbal->setDataRowClass('\APP\Module\Course\DataRow\CourseCatalog');
            $dbal->byCourseId($this->getId());
            $this->catalogs = $dbal->execute();
        }
        return $this->catalogs;
    }
    
    public function getCatalogTree() {
        $catalogs = $this->getCatalogs();
        $root = new CatalogNode();
        $root->setDataRow($this);
        $tree = new UnSortedTree($root);
        foreach ($catalogs as $catalog) {
            $node = new CatalogNode();
            $node->setDataRow($catalog);
            $tree->addByKey($node, $catalog->get('parent_id'));
        }
        return $tree;
    }
    
    /**
     * 获得视频部分信息
     * @return \ORC\DAO\Table\DataRow
     */
    public function getVideo() {
    }
    
    /**
     * 获得所有的材料需求
     * @return \ORC\DAO\Table\DataList
     */
    public function getMaterials() {
        if (!isset($this->materials)) {
            //@todo
        }
    }
    
    public function getAllKnowledgePoints() {
        $catalogs = $this->getCatalogs();
        $catalog_ids = $catalogs->getByName('id');
        $dbal = DBAL::select('course_catalog_kps');
        $dbal->byCourseCatalogId($catalog_ids);
        $list = $dbal->execute();
        $list->groupBy('course_catalog_id');
        
    }
    
    public function getAllAttachments() {
        
    }
}