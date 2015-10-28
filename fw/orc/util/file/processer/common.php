<?php
namespace ORC\Util\File\Processer;
abstract class Common {
	public function getReturnIndex() {
		$classname = get_class($this);
		return strtolower(str_replace("\\", '_', $classname));
	}
}