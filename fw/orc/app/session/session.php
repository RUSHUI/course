<?php
namespace ORC\APP\Session;
use ORC\Util\Container;
abstract class Session extends Container {
	
	public function __construct() {
//		ignore_user_abort(true);
		$this->start();
		register_shutdown_function(array($this, 'onShutDown'));
	}
	
	/**
	 * @return mixed the unique id of the session
	 */
	public abstract function getId();
	
	/**
	 * @return boolean true if session already started
	 */
	public abstract function started();
	/**
	 * start the session
	 * @return boolean true if success. Notice that it'll return true even when called multiple times
	 */
	protected abstract function start();
	
	/**
	 * a callback function to be called when php execution end.
	 * should used for storing the session data
	 * has to be public
	 */
	public abstract function onShutDown();
}