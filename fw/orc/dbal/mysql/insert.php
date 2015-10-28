<?php
namespace ORC\DBAL\MySQL;
class Insert extends \ORC\DBAL\Common\Insert {
	/* (non-PHPdoc)
	 * @see \ORC\DBAL\Common\Insert::getDuplicateSQL()
	 */
	protected function getDuplicateSQL() {
		$sql = '';
		$temp = array();
		foreach ($this->_duplicates as $field) {
			$temp[] = sprintf('%s = VALUES(%s)', $field, $field);
		}
		if ($this->_raw_duplicate) {
		    $temp[] = $this->_raw_duplicate;
		}
		if (count($temp)) {
			$sql = ' ON DUPLICATE KEY UPDATE ' . implode(', ', $temp);
		}
		return $sql;
	}

	
}