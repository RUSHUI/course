<?php
namespace ORC\Util\File\Validator;
class Filesize extends Common implements \ORC\Util\File\Validator {
	protected $_min;
	protected $_max;
	public function __construct($min = null, $max = null) {
		if ($min !== null) {
			$this->_min = (int)$min;
		}
		if ($max !== null) {
			$this->_max = (int)$max;
		}
	}
	/* (non-PHPdoc)
	 * @see \ORC\Util\File\Validator::valid()
	 */
	public function valid(\ORC\Util\File\FileInfo $fileinfo) {
		if ($this->_min !== null) {
			if ($fileinfo->getSize() < $this->_min) {
				throw new Exception('FILESIZE_TOO_SMALL');
			}
		}
		if ($this->_max !== null) {
			if ($fileinfo->getSize() > $this->_max) {
				throw new Exception('FILESIZE_TOO_BIG');
			}
		}
	}
}