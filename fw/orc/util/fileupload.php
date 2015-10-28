<?php
namespace ORC\Util;
use \ORC\util\file\FileInfo;
class FileUpload {
	protected $_processers = array();
	protected $_validators = array();
	
	protected $_name;
	protected $_errors = array();
	protected $_options = array();
	const OPT_ALLOW_FAIL = 'allow_fail';
	const OPT_ALLOW_OVERWRITE = 'allow_overwrite';
	const OPT_AUTO_RENAME = 'auto_rename';//if allow_overwrite set to true, this option has no meaning
	const OPT_USE_RANDOM_NAME = 'use_random_name';//if set to true, allow_overwrite and auto_rename will not in use anymore
	const OPT_PUBLIC = 'public';
	const OPT_FOLDER_DEPTH = 'folder_depth';
	const OPT_FOLDER_LENGTH = 'folder_length';
	public function __construct($name, array $options = array()) {
		$this->_name = $name;
		$this->setOptions($options);
	}
	
	public function setOptions(Array $options) {
		$this->_options = $default_options = $this->getDefaultOptions();
		foreach ($options as $k => $v) {
			if (isset($default_options[$k])) {
				$this->_options[$k] = $v;
			}
		}
		return $this;
	}
	
	public function getOptions() {
		return $this->_options;
	}
	
	/**
	 * 
	 * @return boolean|mixed|multitype:Ambigous <boolean, \ORC\Util\multitype:multitype:, multitype:multitype: FileInfo >
	 */
	public function upload() {
		$files = array();
		$single_upload = true;
		if (isset($_FILES[$this->_name]['tmp_name'])) {
			if (is_array($_FILES[$this->_name]['tmp_name'])) {
				$single_upload = false;
				for($i = 0; $i < count($_FILES[$this->_name]['tmp_name']); $i ++) {
					if ($_FILES[$this->_name]['error'][$i] !== UPLOAD_ERR_OK) {
						$this->_errors[] = array($i, $this->createStandardError($_FILES[$this->_name]['error'][$i]));
						if ($this->_options[self::OPT_ALLOW_FAIL]) {
							continue;
						} else {
							return false;
						}
					}
					$files[$i] = new FileInfo($_FILES[$this->_name]['name'][$i], $_FILES[$this->_name]['size'][$i], $_FILES[$this->_name]['tmp_name'][$i], $_FILES[$this->_name]['type'][$i]);
				}
			} else {
			    if ($_FILES[$this->_name]['error'] !== UPLOAD_ERR_OK) {
			        $this->_errors[] = array(0, $this->createStandardError($_FILES[$this->_name]['error']));
			        return false;
			    }
				$files[0] = new FileInfo($_FILES[$this->_name]['name'], $_FILES[$this->_name]['size'], $_FILES[$this->_name]['tmp_name'], $_FILES[$this->_name]['type']);
			}
		}
		$result = array();
		foreach ($files as $index => $fileinfo) {
			$result[$index] = $this->handleUpload($fileinfo, $index);
			if ($result[$index] === false && (!$this->_options[self::OPT_ALLOW_FAIL])) {
				return false;
			}
		}
		if ($single_upload) {
			return array_pop($result);
		} else {
			return $result;
		}
	}
	
	/**
	 * 
	 * @param \ORC\Util\File\Processer $processer
	 * @param string $return_key
	 * @return \ORC\Util\FileUpload
	 */
	public function addProcesser(\ORC\Util\File\Processer $processer, $return_key = null) {
		if (empty($return_key)) {
			$return_key = $processer->getReturnIndex();
		}
		$this->_processers[$return_key] = $processer;
		return $this;
	}
	
	/**
	 * 
	 * @param \ORC\Util\File\Validator $validator
	 * @return \ORC\Util\FileUpload
	 */
	public function addValidator(\ORC\Util\File\Validator $validator) {
		$this->_validators[] = $validator;
		return $this;
	}
	
	/**
	 * 
	 * @return multitype:string
	 */
	public function getErrors() {
		return $this->_errors;
	}
	
	/**
	 * 
	 * @return boolean
	 */
	public function hasError() {
		return count($this->_errors) > 0;
	}
	
	/**
	 * can be overwrite if you want to use other folders
	 * @see \ORC\APP\Uploader
	 * @param bool $is_public
	 * @return string
	 */
	public static function getFileBasePath($is_public) {
	    if ($is_public) {
	        $folder = DIR_APP_PUBLIC . DIRECTORY_SEPARATOR . 'uploads';
	    } else {
	        $folder = DIR_APP_ROOT . DIRECTORY_SEPARATOR . 'uploads';
	    }
	    if (!file_exists($folder)) {
	        mkdir($folder, '0777');
	    }
	    return $folder;
	}
	/**
	 * @param int $code
	 */
	protected function createStandardError($code) {
		switch ($code) {
			case UPLOAD_ERR_INI_SIZE:
				return 'The uploaded file exceeds the upload_max_filesize';
				break;
			case UPLOAD_ERR_FORM_SIZE:
				return 'The uploaded file exceeds the MAX_FILE_SIZE';
				break;
			case UPLOAD_ERR_PARTIAL:
				return 'The uploaded file was only partially uploaded.';
				break;
			case UPLOAD_ERR_NO_FILE:
				return 'No file was uploaded.';
				break;
			case UPLOAD_ERR_NO_TMP_DIR:
				return 'Missing a temporary folder.';
				break;
			case UPLOAD_ERR_CANT_WRITE:
				return 'Failed to write file to disk.';
				break;
			case UPLOAD_ERR_EXTENSION:
				return 'File upload stopped by extension';
				break;
			default:
				return 'Fail to upload file.';
				break;
		}
	}
	
	/**
	 * upload one file
	 * @param FileInfo $fileinfo
	 * @return boolean|multitype:multitype: FileInfo
	 */
	protected function handleUpload(FileInfo $fileinfo, $index) {
		$result = array();
		try {
			foreach ($this->_validators as $validator) {
				$validator->valid($fileinfo);
			}
		} catch (\ORC\Util\File\Validator\Exception $ex) {
			$this->_errors[] = array($index, $ex->getMessage());
			return false;
		}
		$source = $fileinfo->getPath();
		$destination = $this->getDestName($fileinfo, $index);
		if ($destination && move_uploaded_file($source, $this->getFileBaseDir() . DIRECTORY_SEPARATOR . $destination)) {
			$fileinfo->setPath($destination);
			//comment this line to make sure not overwrite the default filename
			//$fileinfo->setName(array_pop(explode(DIRECTORY_SEPARATOR, $destination)));
			$result['info'] = $fileinfo;
		} else {
			$this->_errors[] = array($index, 'Move uploaded file failed');
			return false;
		}
		foreach ($this->_processers as $key => $processer) {
		    if (!isset($result['extra'])) $result['extra'] = array();
		    $result['extra'][$key] = $processer->handle($this, $fileinfo);
		}
		return $result;
	}
	
	protected function getDefaultOptions() {
		return array(self::OPT_ALLOW_FAIL => true,
				self::OPT_ALLOW_OVERWRITE => false,
				self::OPT_AUTO_RENAME => true,//if allow_overwrite set to true, this option has no meaning
				self::OPT_USE_RANDOM_NAME => true,//if set to true, allow_overwrite and auto_rename will not in use anymore
				self::OPT_PUBLIC => true,
				self::OPT_FOLDER_DEPTH => 2,
				self::OPT_FOLDER_LENGTH => 2,
		);
	}
	
	public function getFileBaseDir() {
		return self::getFileBasePath($this->_options[self::OPT_PUBLIC]);
	}
	
	/**
	 * get the destination filename
	 * @param \ORC\Util\File\FileInfo $fileinfo
	 * @return string|boolean
	 */
	protected function getDestName(\ORC\Util\File\FileInfo $fileinfo, $fileIndex) {
		$base = $this->getFileBaseDir();
		if ($this->_options[self::OPT_USE_RANDOM_NAME]) {
			do {
				$filename = $this->createRandomName($fileinfo);
				$dest = $this->getDestFolder($filename) . DIRECTORY_SEPARATOR . $filename;
			} while (file_exists($base . DIRECTORY_SEPARATOR . $dest));
			return $dest;
		}
		$filename = $fileinfo->getName();
		if ($this->_options[self::OPT_ALLOW_OVERWRITE]) {
			return $this->getDestFolder($filename) . DIRECTORY_SEPARATOR . $filename;
		}
		$index = 0;
		do {
			$dest = $this->getDestFolder($filename) . DIRECTORY_SEPARATOR . $filename;
			if (file_exists($base . DIRECTORY_SEPARATOR . $dest)) {
				if ($this->_options[self::OPT_AUTO_RENAME]) {
					//get a new filename
					$filename = sprintf('%s_%d.%s', $fileinfo->getNameWithoutExtension(), $index ++, $fileinfo->getExtension());
					$dest = $this->getDestFolder($filename) . DIRECTORY_SEPARATOR . $filename;
					//pre($dest);
				} else {
					$this->_errors[] = array($fileIndex, 'file already exists');
					return false;
				}
			} else {
				break;
			}
		} while (true);
		return $dest;
	}
	
	protected function createRandomName(\ORC\Util\File\FileInfo $fileinfo) {
		$name = md5(UUID::guid()) . '.' . $fileinfo->getExtension();
		return $name;
	}
	
	protected function getDestFolder($filename) {
		$base = $this->getFileBaseDir();
		$basename = pathinfo($filename);
		$basename = $basename['filename'];
		if (preg_match('/^[a-z0-9_]+$/i', $basename) && strlen($basename) > $this->_options[self::OPT_FOLDER_DEPTH] * $this->_options[self::OPT_FOLDER_LENGTH]) {
			$seed = $basename;
		} else {
			$seed = md5($filename);
		}
		//pre($seed);
		$folders = array();
		for($i = 0; $i < $this->_options[self::OPT_FOLDER_DEPTH]; $i++) {
			$folder = substr($seed, $this->_options[self::OPT_FOLDER_LENGTH] * $i, $this->_options[self::OPT_FOLDER_LENGTH]);
			if (!file_exists($base . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $folders) . DIRECTORY_SEPARATOR . $folder)) {
				mkdir($base . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $folders) . DIRECTORY_SEPARATOR . $folder, 0777);
			}
			$folders[] = $folder;
		}
		return implode(DIRECTORY_SEPARATOR, $folders);
	}
}