<?php
use ORC\MVC\Action;
use ORC\DBAL\DBAL;
class Second_Demo_Action extends Action {
	
	public function execute() {

		$rs = $this->getRequest();
		switch($rs->get('action')){
			case "insert":
				$dbal = DBAL::insert('user');
				$dbal->set('username', $rs->get('username'));
                $dbal->set('password', $rs->get('password'));
				$id = $dbal->execute();
                return $this->HTMLRedirect($this->generateURL('Second.Demo'), '注册成功', '注册成功');
                break;
			case "list":
                $page = $rs->get("page");
                $model = $this->getModel('Second.User');
                $model->loadUsers($page);
				return $this->HTMLView('Second.Add');
				break;
            case "details":
                $rs = $this->getRequest();
                $uid = $rs->get('uid');
                $model = $this->getModel('Second.Details');
                $model->loadDetails($uid);
                return $this->HTMLView('Second.Details');
                break;
			default:
				return $this->HTMLView('Second.List');
				break;
		}		
	}
}
