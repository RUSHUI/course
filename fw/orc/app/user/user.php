<?php
namespace ORC\APP\User;
use ORC\DBAL\DBAL;
use ORC\Util\Container;
use ORC\Core\Config;
class User extends Container implements IUser {
	
	const SUPER_USER_ID = 1;
	
	const DEFAULT_SECRET = 'ebb4295d4790e41ef654abd0d885cb0e';
	/**
	 * 
	 * @var Roles
	 */
	protected $roles;
	
	
	public function __construct() {
	    $this->logout();
	    //pre(__LINE__, $this->roles);
	}
	
	/* (non-PHPdoc)
	 * @see \ORC\APP\User\IUser::getId()
	 */
	public function getId() {
		return $this->get('id');
	}

	/* (non-PHPdoc)
	 * @see \ORC\APP\User\IUser::canDo()
	 */
	public function canDo($permission) {
		// TODO Auto-generated method stub
		if ($this->isSuperAdmin()) return true;
		if (isset($this->roles)) {
			return $this->roles->canDo($permission);
		}
		return false;
	}

	/* (non-PHPdoc)
	 * @see \ORC\APP\User\IUser::encryptPassword()
	 */
	public function encryptPassword($password) {
		$secret = Config::getInstance()->get('app.user.secret');
		$result = substr(md5($password . $secret) . md5($secret . substr($password, 0, 1)), 0, 40);
		//pre($result);
		return $result;
	}

	/* (non-PHPdoc)
	 * @see \ORC\APP\User\IUser::getRoles()
	 */
	public function getRoles() {
		if (!isset($this->roles)) {
			$roles = Roles::getAllRoles();
			$this->roles = new Roles();
			//$temp = new Roles();
			if ($this->isLogined()) {
				$this->roles[ROLE::LOGIN_USER_ROLE_ID] = $roles[ROLE::LOGIN_USER_ROLE_ID];//the user will have default role as 'User'
				//$temp[ROLE::LOGIN_USER_ROLE_ID] = $roles[ROLE::LOGIN_USER_ROLE_ID];
			}
			$dbal = DBAL::select(DB::getUserRoleTableName())->byUserId($this->getId());
			$result = $dbal->execute();
			if (!empty($result)) {
				foreach($result as $dataRow) {
					$this->roles[$dataRow->get('role_id')] = $roles[$dataRow->get('role_id')];
					//$temp[$dataRow->get('role_id')] = $roles[$dataRow->get('role_id')];
				}
			}
			//$this->roles = $temp;
		}
		return $this->roles;
	}

	/* (non-PHPdoc)
	 * @see \ORC\APP\User\IUser::flushRoles()
	 */
	public function flushRoles() {
		unset($this->roles);
	}

	/* (non-PHPdoc)
	 * @see \ORC\APP\User\IUser::login()
	 */
	public function login($email, $password) {
	    $this->flushRoles();
		$dbal = DBAL::select(DB::getUserTableName())->byEmail($email);
		$result = $dbal->getOne();
		if ($result instanceof \ORC\DAO\Table\DataRow) {
			if ($result->get('password') == $this->encryptPassword($password)) {
				$this->_data = $result->getAllData();
				return true;
			}
		}
		return false;
	}

	/* (non-PHPdoc)
     * @see \ORC\APP\User\IUser::autoLogin()
     */
    public function autoLogin($id)
    {
        $this->flushRoles();
        $dbal = DBAL::select(DB::getUserTableName())->pk($id);
        $result = $dbal->getOne();
        if ($result instanceof \ORC\DAO\Table\DataRow) {
            $this->_data = $result->getAllData();
            return true;
        }
        return false;
    }

	/* (non-PHPdoc)
	 * @see \ORC\APP\User\IUser::logout()
	 */
	public function logout() {
		$this->removeAll();
		$this->flushRoles();
		$this->set('id', self::ID_ANONYMOUS);
	}
	/* (non-PHPdoc)
	 * @see \ORC\APP\User\IUser::isLogined()
	 */
	public function isLogined() {
		return $this->getId() > 0;
	}

	public function isSuperAdmin() {
		return $this->getId() == self::SUPER_USER_ID;
	}
}