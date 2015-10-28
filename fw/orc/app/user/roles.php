<?php
namespace ORC\APP\User;
use ORC\Exception\Exception;
use ORC\DBAL\DBAL;
class Roles implements \ArrayAccess {
	protected $_roles = array();
	/* (non-PHPdoc)
	 * @see ArrayAccess::offsetExists()
	 */
	public function offsetExists($offset) {
		return isset($this->_roles[$offset]);
	}

	/* (non-PHPdoc)
	 * @see ArrayAccess::offsetGet()
	 */
	public function offsetGet($offset) {
		return isset($this->_roles[$offset]) ? $this->_roles[$offset] : null;
	}

	/* (non-PHPdoc)
	 * @see ArrayAccess::offsetSet()
	 */
	public function offsetSet($offset, $value) {
		if (!($value instanceof IRole)) {
			throw new Exception('Wrong Role type used');
		}
		if (is_null($offset)) {
			$this->_roles[] = $value;
		} else {
			$this->_roles[$offset] = $value;
		}
	}

	/* (non-PHPdoc)
	 * @see ArrayAccess::offsetUnset()
	 */
	public function offsetUnset($offset) {
		unset($this->_roles[$offset]);
	}
	
	/**
	 * 
	 * @param string $permission
	 * @return boolean
	 */
	public function canDo($permission) {
		foreach ($this->_roles as $role) {
			if ($role->canDo($permission)) {
				return true;
			}
		}
		return false;
	}
	
	public function getRoleId($role_name) {
	    foreach ($this->_roles as $role_id => $role) {
	        if (strcasecmp($role_name, $role->getName()) == 0) {
	            return $role_id;
	        }
	    }
	    return 0;
	}
	
	
	public function hasRole($role_name) {
	    if (is_int($role_name)) {
	        return isset($this->_roles[$role_name]);
	    }
	    return $this->getRoleId($role_name) ? true : false;
	}
	
	/**
	 * @return \ORC\App\User\Roles
	 */
	public static function getAllRoles() {
		static $roles;
		if (!isset($roles)) {
			$roles = new self();
			$dbal = DBAL::select(DB::getRoleTableName());
			$result = $dbal->execute();
			foreach ($result as $row) {
				$role = new Role($row);
				//$this->_roles[$role->getId()] = $role;
				$roles[$role->getId()] = $role;
			}
		}
		return $roles;
	}
	
	protected function reset() {
		$this->_roles = array();
	}
}