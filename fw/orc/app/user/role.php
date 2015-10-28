<?php
namespace ORC\APP\User;
use ORC\DAO\Table\DataRow;
use ORC\Exception\SystemException;
use ORC\Util\Container;
class Role extends Container implements IRole {
	const LOGIN_USER_ROLE_ID = 1;
	
	protected $_permission;
	/* (non-PHPdoc)
	 * @see \ORC\APP\User\IRole::getId()
	 */
	public function getId() {
		return $this->get('id');
	}

	/* (non-PHPdoc)
	 * @see \ORC\APP\User\IRole::getName()
	 */
	public function getName() {
		return $this->get('name');
	}

	
	/* (non-PHPdoc)
	 * @see \ORC\APP\User\IRole::canDo()
	 */
	public function canDo($permission) {
		return $this->_permission->hasPermission($permission);
	}

	public function __construct(DataRow $row) {
		if ($row->getTable()->getTableName() !== DB::getRoleTableName()) {
			throw new SystemException('Wrong Role Table');
		}
		$this->_data = $row->getAllData();
		//load permission
		$this->loadPermission();
	}
	
	protected function loadPermission() {
		$permissions = Permission::getAllPermissions();
		$role_id = $this->getId();
		if (isset($permissions[$role_id])) {
			$this->_permission = new Permission($permissions[$role_id]);
		} else {
			$this->_permission = new Permission();
		}
	}
}