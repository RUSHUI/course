<?php
namespace ORC\DAO\Exception;
use ORC\DAO\Dao;
class SQLException extends \ORC\DAO\Exception\Exception {
	public function __construct($message, Dao $dao = null, $sql = null) {
		if (!empty($dao)) {
			if ($dao->isDebug()) {
				$message .= sprintf("\nSQL: %s", $sql);
			}
		}
		parent::__construct($message, $dao);
	}
}