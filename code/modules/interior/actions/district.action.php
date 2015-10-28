<?php
use ORC\API\Interior\Server\APIAction;
use APP\Module\District\District;
class Interior_District_Action extends APIAction {
    /* (non-PHPdoc)
     * @see \ORC\API\Interior\Server\APIAction::execute()
     */
    public function execute()
    {
        $request = $this->getRequest();
        switch ($request->get(0)) {
            case 'get':
                $districtLoader = new District();
                $list = $districtLoader->getAll();
                $list->sort('name', true, true);
                $districts = array();
                foreach ($list as $row) {
                    $districts[$row->get('id')] = $row->getAllData();
                }
                return $this->send($districts);
                break;
        }
    }
}