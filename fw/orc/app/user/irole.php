<?php
namespace ORC\APP\User;
interface IRole {
	/**
	 * @return int the role id
	 */
	public function getId();
	/**
	 * @return string the role name
	 */
	public function getName();
	
	/**
	 * 
	 * @param string $permission
	 * @return bool
	 */
	public function canDo($permission);
}