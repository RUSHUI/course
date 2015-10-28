<?php
use ORC\API\Interior\Server\APIAction;
use ORC\API\Interior\Exception\Exception;
use APP\Module\Subject\Subject;
class Interior_Subject_Action extends APIAction {
    public function execute() {
        $request = $this->getRequest();
        $action = $request->get(0);
        switch ($action) {
            case 'get':
                $subjectLoader = new Subject();
                $subjects = $subjectLoader->getAllSubjects(true);
                $sub_subjects = $subjectLoader->getSubSubjects(false, true);
                return $this->send(array('subjects' => $subjects, 'sub_subjects' => $sub_subjects));
                break;
            default:
                throw new Exception('未知的动作', Exception::CODE_ACTION_NOTFOUND);
        }
    }
}