<?php
namespace ORC\APP\User;
interface IUser {
    const ID_ANONYMOUS = 0;
	/**
	 * get the user unique id
	 */
	public function getId();
	/**
	 * login user. May throw Exceptions
	 * @param string $username the username, could be a string, an email, etc.
	 * @param string $password the original password
	 * @return user id if success, false if failed
	 */
	public function login($login, $password);
	
	/**
	 * login user without any check
	 * @param int $id
	 */
	public function autoLogin($id);
	
	/**
	 * logout current user
	 */
	public function logout();
	
	/**
	 * 
	 * @param string $password
	 * @return string encrypted password
	 */
	public function encryptPassword($password);
	
	/**
	 * @return bool whether the user can do some certain action
	 * @param string $permission
	 */
	public function canDo($permission);
	
	/**
	 * @return \ORC\APP\User\Roles
	 */
	public function getRoles();
	
	/**
	 * to flush the current role
	 * used when user change roles
	 */
	public function flushRoles();
	/**
	 * @return bool
	 */
	public function isLogined();
	
	/**
	 * get the value of certain key
	 * @param string $key
	 * @return mixed|null
	 */
	public function get($key);
}