<?php
namespace ORC\Util\File;
class FileInfo {
	/**
	 * @var string
	 */
	protected $name;
	
	/**
	 * name without extension
	 * @var string
	 */
	protected $basename;
	/**
	 * @var int
	 */
	protected $size;
	/**
	 * @var string
	 */
	protected $extension;
	/**
	 * 
	 * @var string
	 */
	protected $path;

	/**
	 * 
	 * @var string
	 */
	protected $type;
	
	public function __construct($name, $size, $path, $type) {
		$this->setName($name);
		$this->setSize($size);
		$this->setPath($path);
		$this->setType($type);
		$info = pathinfo($name);//pre($name, $info);
		$this->setExtension($info['extension']);
		$this->basename = $info['filename'];
	}
	/**
	 * @return the $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return the $size
	 */
	public function getSize() {
		return $this->size;
	}

	/**
	 * @return the $extension
	 */
	public function getExtension() {
		return $this->extension;
	}

	/**
	 * @return the $path
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * @return the $type
	 */
	public function getType() {
		return $this->type;
	}

	public function getNameWithoutExtension() {
		return $this->basename;
	}
	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @param number $size
	 */
	public function setSize($size) {
		$this->size = $size;
	}

	/**
	 * @param string $extension
	 */
	public function setExtension($extension) {
		$this->extension = $extension;
	}

	/**
	 * @param string $path
	 */
	public function setPath($path) {
		$this->path = $path;
	}

	/**
	 * @param string $type
	 */
	public function setType($type) {
		$this->type = $type;
	}

	public function isImage() {
		if (file_exists($this->path)) {
			$info = @getimagesize($this->path);
			if ($info !== false) {
				return true;
			}
		}
		return false;
	}
}