<?php
namespace ORC\APP\Session;
/**
 * this is a fake session handler used when you don't need a session
 * @author Zhou Yanqin
 *
 */
class None extends Session {
	
	public function __construct() {
		//do nothing here
	}
	/* (non-PHPdoc)
	 * @see \ORC\APP\Session\Session::getId()
	 */
	public function getId() {
		return 1;
	}

	/* (non-PHPdoc)
	 * @see \ORC\APP\Session\Session::started()
	 */
	public function started() {
		return true;
	}

	/* (non-PHPdoc)
	 * @see \ORC\APP\Session\Session::onShutDown()
	 */
	public function onShutDown() {
		//do nothing here
	}

	/* (non-PHPdoc)
	 * @see \ORC\APP\Session\Session::start()
	 */
	protected function start() {
		return true;
	}

	
}