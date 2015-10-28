<?php
namespace ORC\APP;
use ORC\Core\Config;
use ORC\Exception\Exception;
use ORC\Exception\SystemException;
final class Session {
	private static $session;
	
	/**
	 * get current session
	 * notice that the session will be started when it's first called
	 * **so we have to start the session in handler's constructor**
	 * @throws SystemException
	 * @throws Exception
	 * @return \ORC\APP\Session\Session
	 */
	public static function getInstance() {
		if (isset(self::$session)) {
			return self::$session;
		}
		$config = Config::getInstance();
		$handler = $config->get('app.session.handler');
		if (empty($handler)) {
			$handler = 'php';
		}
		if ($handler == 'custom') {
			$classname = $config->get('app.session.classname');
			if (empty($classname)) {
				throw new SystemException('session handler classname is empty');
			}
		} else {
			$classname = "\\ORC\\APP\\Session\\" . $handler;
		}
		try {
			$session = new $classname();
			if ($session instanceof \ORC\APP\Session\Session) {
				self::$session = $session;
				return self::$session;
			}
		} catch (\Exception $ex) {
			throw new Exception('can not find session handler');
		}
	}
}