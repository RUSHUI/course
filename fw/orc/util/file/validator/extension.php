<?php
namespace ORC\Util\File\Validator;
class Extension extends Common implements \ORC\Util\File\Validator {
	protected $_exts;
	public function __construct(Array $extensions) {
		$this->_exts = array_map('strtolower', $extensions);
	}
	/* (non-PHPdoc)
	 * @see \ORC\Util\File\Validator::valid()
	 */
	public function valid(\ORC\Util\File\FileInfo $fileinfo) {
		if (!in_array(strtolower($fileinfo->getExtension()), $this->_exts)) {
			throw new Exception('EXTENSION_NOT_VALID');
		}
	}
}