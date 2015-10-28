<?php
namespace ORC\APP\User;
class DB {
	public static function getUserTableName() {
		static $tablename;
		if (!isset($tablename)) {
			//@todo get from config
			$tablename = 'users';
		}
		return $tablename;
	}
	
	public static function getUserRoleTableName() {
		static $tablename;
		if (!isset($tablename)) {
			//@todo get from config
			$tablename = 'user_roles';
		}
		return $tablename;
	}
	
	public static function getRoleTableName() {
		static $tablename;
		if (!isset($tablename)) {
			//@todo get from config
			$tablename = 'roles';
		}
		return $tablename;
	}
	
	public static function getPermissionTableName() {
		static $tablename;
		if (!isset($tablename)) {
			//@todo get from config
			$tablename = 'permissions';
		}
		return $tablename;
	}
}