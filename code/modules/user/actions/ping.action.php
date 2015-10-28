<?php
use ORC\MVC\Action;
class User_Ping_Action extends Action {
    public function execute() {
        $me = $this->getMe();
        if ($me->isLogined()) {
            return $this->JSONReturn(array('status' => 'OK'));
        }
        return $this->JSONReturn(array('status' => 'ERR'));
    }
}