<?php
use ORC\MVC\Model;
use ORC\DBAL\DBAL;
use ORC\Util\Pagination;
class Second_User_Model extends Model {
    public function loadUsers($page) {
        $dbal = DBAL::select('user');
        $dbal->setPage($page,'2');
        $dbal->setDataRowClass('\\APP\\Module\\Test\\DataRow\\Test');
        $list = $dbal->execute();
        $pagination = new Pagination($list, $dbal->getTotalCount(), $page, 2);
        $this->set('list', $list);
        $this->set('patgination',$pagination);
    }
}