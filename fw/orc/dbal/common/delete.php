<?php
namespace ORC\DBAL\Common;
use ORC\DAO\Dao;
use ORC\Util\Callback;
use ORC\Util\Observer;
use ORC\Util\Logger;
class Delete extends Common {
	
	/**
	 * 
	 * @return number
	 */
	public function execute() {
		Callback::get('dbal.delete.pre_execute')->call($this);
		Observer::getObserver('dbal.delete.pre_execute')->notify();
		if ($this->_has_empty_in) {
		    $count = 0;
		} else {
    		$dao = $this->getDao();
    		$this->prepare($dao);
    		$this->executeDao($dao);
    		$count = $dao->rowCount();
		}
		Callback::get('dbal.delete.post_execute')->call($this, $dao, $count);
		Observer::getObserver('dbal.delete.post_execute')->notify();
		return $count;
	}
	/* (non-PHPdoc)
	 * @see \ORC\DBAL\Common\Common::buildStatement()
	 */
	protected function buildStatement() {
		$where = $this->buildWhere();
		if ($where == '') {
			Logger::getInstance('DBAL')->addWarning('delete from ' . $this->_table->getTableName() .' without WHERE statement!');
		}
		$sql = 'DELETE FROM ' . $this->_table->getTableName() . $where;
		return $sql;
	}

	protected function executeDao(Dao $dao) {
		$values = $this->getWhereValues();
		return $dao->execute($values);
	}
	
}