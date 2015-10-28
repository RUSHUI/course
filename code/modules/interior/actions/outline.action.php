<?php
use ORC\API\Interior\Server\APIAction;
/**
 * 内部调用接口
 * url 类似/interior/outline/get
 * @author pal
 * @todo 没开始做
 */
class Interior_OutLine_Action extends APIAction {
    public function execute() {
        $request = $this->getRequest();
        $action = $request->get(0);
        switch ($action) {
            case 'get':
                return $this->send(array('k1' => 'v1', 'k2' => 'v2'));
                break;
            default:
                return false;
        }
    }
}