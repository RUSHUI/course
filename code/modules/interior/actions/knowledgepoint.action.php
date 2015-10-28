<?php
use ORC\API\Interior\Server\APIAction;
use APP\Module\KnowledgePoint\KnowledgePoints;
class Interior_KnowledgePoint_Action extends APIAction {
 /* (non-PHPdoc)
     * @see \ORC\API\Interior\Server\APIAction::execute()
     */
    public function execute()
    {
        $request = $this->getRequest();
        switch ($request->get(0)) {
            case 'get':
                $criteria = array();
                if ($request->get('subject_id', 'posint')) {
                    $criteria['subject_id'] = $request->get('subject_id');
                }
                if ($request->get('sub_subject_id', 'posint')) {
                    $criteria['sub_subject_id'] = $request->get('special_id');
                }
                $knowledgePointLoader = new KnowledgePoints();
                $list = $knowledgePointLoader->search($criteria, 0, 0);
                $knowledgePointLoader->loadExtra($list);
                $data = array();
                foreach ($list as $row) {
                    $data[$row['id']] = $row->getAllData();
                }
                return $this->send($data);
                break;
            default:
                
                break;
        }

    }

    
}