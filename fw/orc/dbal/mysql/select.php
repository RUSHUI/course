<?php
namespace ORC\DBAL\MySQL;
use ORC\DAO\Dao;
class Select extends \ORC\DBAL\Common\Select {
	/* (non-PHPdoc)
	 * @see \ORC\DBAL\Common\Select::limit()
	 */
	protected function limit(array $args) {
		if (count($args) == 1) {
			$page = 1;
			$pagesize = (int)$args[0];
		} else {
			$page = (int)$args[0];
			$pagesize = (int)$args[1];
		}
		if ($page < 1) {
			$page = 1;
		}
		$this->_limit = sprintf('%d, %d', ($page - 1) * $pagesize, $pagesize);
		return $this;
	}

	/* (non-PHPdoc)
	 * @see \ORC\DBAL\Common\Select::getTotalCount()
	 */
	public function getTotalCount() {
		if (!isset($this->_total_count)) {
			if (!empty($this->_limit)) {
				$dao = $this->getDao();
				$old_index = $dao->getCurrentStatementIndex();
				$index = $dao->query('SELECT FOUND_ROWS()');
				$result = $dao->fetch(Dao::FETCH_NUM);
				$dao->switchStatement($old_index);
				return $this->_total_count = (int)$result[0];
			}
		}
		return $this->_total_count;
	}

	protected function buildStatement() {
		if (!empty($this->_limit)) {
			$wheres = $this->buildWhere();
			$sql = 'SELECT SQL_CALC_FOUND_ROWS ' . $this->getSelect() . ' FROM ' . $this->_table->getTableName() . $this->buildWhere();
			$sql .= $this->buildOrderBy();
			$sql .= ' LIMIT ' . $this->_limit;
			return $sql;
		} else {
			return parent::buildStatement();
		}
	}
	
}