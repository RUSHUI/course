<?php
namespace APP\Module\Course;
use ORC\DBAL\DBAL;
class Loader {
    /**
     * 
     * @param int $course_id
     * @return \APP\Module\Course\DataRow\Course
     */
    public function get($course_id) {
        $dbal = DBAL::select('courses');
        $dbal->setDataRowClass('\APP\Module\Course\DataRow\Course');
        $dbal->byId($course_id);
        return $dbal->getOne();
    }
}