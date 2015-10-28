<?php
namespace ORC\APP;
use ORC\Core\Config;
use ORC\DAO\Table;
use ORC\DBAL\DBAL;
use ORC\Util\Util;
class Uploader extends \ORC\Util\FileUpload {
	const DEFAULT_TABLE_NAME = 'attachments';
	protected $_extra;
	
	/**
	 * 
	 * @param string $name
	 * @param array $options
	 * @param array $extra
	 */
	public function __construct($name, array $options = array(), Array $extra = array()) {
		$this->_extra = $extra;
		parent::__construct($name, $options);
	}
	
	public function upload() {
		$result = parent::upload();
		if ($result) {
			if (isset($result['info'])) {
				$this->saveToDb($result);
			} else {
				foreach ($result as $k => $v) {
					$this->saveToDb($v, $k);
				}
			}
		}
		return $result;
	}
	
	protected function saveToDb(Array $result, $index = 0) {
		$table = $this->getTable();
		$fileInfo = $result['info'];
		$dbal = DBAL::insert($table);
		$dbal->set(array('name' => $fileInfo->getName(),
					'filepath' => $fileInfo->getPath(),
					'mime' => $fileInfo->getType(),
					'filesize' => $fileInfo->getSize(),
					'created' => Util::getNow(),
					'options' => serialize($this->getOptions())
		));
		if (!empty($this->_extra)) {
		    foreach ($this->_extra as $k => $v) {
		        //@todo consider to add some callback feature
		        $dbal->set($k, $v);
		    }
		}
		if (method_exists($this, 'preSaveToDb')) {
		    $dbal = $this->preSaveToDb($dbal, $result, $index);
		}
		return $dbal->execute();
	}
	
	/**
	 * should be overwrite if want to use other tablename
	 * @return \ORC\DAO\Table
	 */
	protected function getTable() {
		static $table;
		if (!isset($table)) {
			$tablename = Config::getInstance()->get('app.file.tablename');
			if (empty($tablename)) {
				$tablename = self::DEFAULT_TABLE_NAME;
			}
			$table = new Table($tablename);
		}
		return $table;
	}
}