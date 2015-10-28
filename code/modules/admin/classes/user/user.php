<?php
namespace APP\Module\Admin\User;
use ORC\APP\Session;
use ORC\Util\Container;
use ORC\DBAL\DBAL;
use APP\Module\Admin\User\Permission\Common;
use ORC\Exception\SystemException;
class User extends Container {
    const SESSION_KEY = 'admin_user';
    private static $user;
    
    /**
     * 
     * @return \APP\Module\Admin\User\User
     */
    public static function getInstance() {
        if (!isset(self::$user)) {
            $session = Session::getInstance();
            $user = $session->get(self::SESSION_KEY);
            if (!($user instanceof User)) {
                $user = new self();
            }
        }
        return self::$user = $user;
    }
    
    private function __construct() {
        $this->reset();
    }
    
    public function getId() {
        return $this->get('id');
    }
    
    public function login($username, $password) {
        //@TODO
    }
    
    public function logout() {
        $this->reset();
    }
    
    public function canDo($module_name, $op = Common::OP_VIEW_ANY, $extra = 0, $user_id = 0) {
        $modules = Common::getAllModules();
        $module_id = array_search($module_name, $modules);
        if ($module_id === false) {
            throw new SystemException('未知的模块', $module_name);
        }
        $role_ids = $this->getRoles();
        $role_permissions = Common::getRolePermissions();
        foreach ($role_ids as $role_id) {
            $role_permission = $role_permissions[$role_id];
            if (is_array($role_permission)) {
                foreach ($role_permission as $permission) {
                    $canDo = false;
                    if ($module_id != $permission['module_id']) {
                        continue;
                    }
                    if ($permission['op'] == Common::OP_ALL) {
                        //所有权限
                        $canDo = true;
                    } elseif ($permission['op'] == $op) {
                        switch ($op) {
                            case Common::OP_DELETE_OWN:
                            case Common::OP_UPDATE_OWN:
                            case Common::OP_VIEW_OWN:
                                if ($user_id == $this->getId()) {
                                    $canDo = true;//是本人
                                }
                                break;
                            default:
                                $canDo = true;
                        }
                        $canDo = true;
                    } else {
                        switch ($op) {
                            case Common::OP_DELETE_OWN:
                                if ($permission['op'] == Common::OP_DELETE_ANY) {
                                    $canDo = true;
                                }
                                break;
                            case Common::OP_UPDATE_OWN:
                                if ($permission['op'] == Common::OP_UPDATE_ANY) {
                                    $canDo = true;
                                }
                                break;
                            case Common::OP_VIEW_OWN:
                                if ($permission['op'] == Common::OP_VIEW_ANY) {
                                    $canDo = true;
                                }
                                break;
                        }
                    }
                    if (!$canDo) {
                        continue;
                    }
                    //检查extra
                    if ($extra == 0 || $extra == $permission['extra']) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
    
    public function set($k, $v) {
        throw new SystemException('不允许在外部修改user里面的值');
    }
    private function saveToSession() {
        $session = Session::getInstance();
        $session->set(self::SESSION_KEY, $this);
    }
    private function getRoles() {
        //read the role from table every time to make sure it's correct
        static $roles;
        if (isset($roles)) {
            return $roles;
        }
        $dbal = DBAL::select('admin_user_roles');
        $dbal->byUserId($this->getId());
        $roles = $dbal->execute()->getByName('role_id');
        return $roles;
    }
    
    private function reset() {
        $this->_data = array('id' => 0);
        $this->saveToSession();
    }
}