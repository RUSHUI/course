<?php
namespace ORC\APP\User;
use ORC\DBAL\DBAL;
class Permission {
	protected $_permissions;
	public static function getAllPermissions() {
		static $permissions;
		if (!isset($permissions)) {
			$permissions = array();
			$result = DBAL::select(DB::getPermissionTableName())->execute();
			foreach ($result as $dataRow) {
				$permissions[$dataRow->get('role_id')] = explode(',', $dataRow['permissions']);
			}
		}
		return $permissions;
	}
	
	public static function flushCache() {
	    return true;
	}
	
	public function __construct(Array $permissions = array()) {
		$this->_permissions = $permissions;
	}
	
	public function addPermission($permission) {
		if (false === array_search($permission, $this->_permissions)) {
			$this->_permissions[] = $permission;
		}
	}
	
	public function hasPermission($permission) {
		return (false !== array_search($permission, $this->_permissions));
	}
}