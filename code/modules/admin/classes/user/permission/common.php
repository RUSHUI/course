<?php
namespace APP\Module\Admin\User\Permission;
use ORC\DAO\Util;
class Common {
    const OP_ALL = 0;
    const OP_CREATE = 1;
    const OP_VIEW_ANY = 2;
    const OP_UPDATE_ANY = 3;
    const OP_DELETE_ANY = 4;
    const OP_VIEW_OWN = 5;
    const OP_UPDATE_OWN = 6;
    const OP_DELETE_OWN = 7;
    public static function getAllModules() {
        static $modules;
        if (!isset($modules)) {
            $list = Util::getTableData('admin_modules');
            $modules = $list->toArray('id', 'name');
        }
        return $modules;
    }
    
    public static function getALLDepartments() {
        static $departments;
        if (!isset($departments)) {
            $list = Util::getTableData('admin_departments');
            $departments = $list->toArray('id');
        }
        return $departments;
    }
    
    public static function getPermissions() {
        static $permissions;
        if (!isset($permissions)) {
            $list = Util::getTableData('admin_permissions');
            $permissions = $list->toArray('id');
        }
        return $permissions;
    }
    
    public static function getRolePermissions() {
        static $role_permissions;
        if (!isset($role_permissions)) {
            $list = Util::getTableData('admin_role_perms');
            $data = $list->groupBy('role_id');
            $role_permissions = array();
            $permissions = self::getPermissions();
            foreach ($data as $role_id => $value) {
                if (!isset($role_permissions[$role_id])) {
                    $role_permissions[$role_id] = array();
                }
                foreach ($value as $row) {
                    $role_permissions[$role_id][] = $permissions[$row['perm_id']]->getAllData();
                }
            }
        }
        return $role_permissions;
    }
    
    public static function getAllRoles() {
        static $roles;
        if (!isset($roles)) {
            $list = Util::getTableData('admin_roles');
            $roles = $list->toArray('id', 'name');
        }
        return $roles;
    }
}