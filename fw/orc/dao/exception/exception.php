<?php
namespace ORC\DAO\Exception;
use ORC\DAO\Dao;
use ORC\Exception\ExceptionCode;
class Exception extends \ORC\Exception\Exception {
	protected $dao;
	public function __construct($message, Dao $dao = null, \PDOException $previous = null) {
		$this->dao = $dao;
		parent::__construct($message, ExceptionCode::DAO, $previous);
	}
	
	/**
	 * @return \ORC\DAO\Dao
	 */
	public function getDao() {
		return $this->dao;
	}
}