<?php
namespace ORC\APP\Session;
use ORC\Application;
class php extends Session {
	const SESSION_KEY = '__data__';
	private $session_started = false;
	/* (non-PHPdoc)
	 * @see \ORC\APP\Session\Session::getId()
	 */
	public function getId() {
		return session_id();
	}

	/* (non-PHPdoc)
	 * @see \ORC\APP\Session\Session::started()
	 */
	public function started() {
		return $this->session_started;
	}

	/* (non-PHPdoc)
	 * @see \ORC\APP\Session\Session::onShutDown()
	 */
	public function onShutDown() {
		$_SESSION[$this->getSessionKey()] = serialize($this->getAllData());
	}

	/* (non-PHPdoc)
	 * @see \ORC\APP\Session\Session::start()
	 */
	protected function start() {
	   if ($this->started()) {
            return true;
        }
	    $session_key = $this->getSessionKey();
		if (session_start()) {
			$this->session_started = true;
			//get the data out
			if (isset($_SESSION[$session_key])) {
				$data = @unserialize($_SESSION[$session_key]);
				if (is_array($data)) {
					$this->_data = $data;
				}
			}
		}
		return $this->session_started;
	}
	
	protected function getSessionKey() {
	    return sprintf('%s%s', Application::getApp()->getName(), static::SESSION_KEY);
	}
}