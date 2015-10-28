<?php
namespace ORC\APP;
use ORC\Core\Config;
use ORC\Exception\SystemException;
class User {
	const SESSION_KEY = '__user__';
	/**
	 * 
	 * @throws SystemException
	 * @return \ORC\APP\User\IUser
	 */
	public static function me() {
		//first try to restore the current user
		$user = Session::getInstance()->get(self::SESSION_KEY);
		if (empty($user) || (!($user instanceof \ORC\APP\User\IUser))) {
			//create a new user object
		    $classname = Config::getInstance()->get('app.user.classname');
		    if (empty($classname)) {
		        $classname = "\\ORC\\APP\\User\\User";
		    }
			if (class_exists($classname)) {
				$user = new $classname();
				if ($user instanceof \ORC\APP\User\IUser) {
					Session::getInstance()->set(\ORC\APP\User::SESSION_KEY, $user);
					return $user;
				}
			}
			throw new SystemException('Unknown User Class', $user);
		}
		return $user;
	}
}