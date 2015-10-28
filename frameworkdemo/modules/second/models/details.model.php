<?php
use ORC\MVC\Model;
use ORC\DBAL\DBAL;
use ORC\MVC\Action;
class Second_Details_Model extends Model {
    public function loadDetails($uid) {

        $dbal = DBAL::select('default.user');
        $dbal->setDataRowClass('\\APP\\Module\\Test\\DataRow\\Test');
        $dbal->byId($uid);
        $rs = $dbal->getOne();
        $this->set('Details', $rs);
    }
}