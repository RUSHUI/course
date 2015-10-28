<?php
namespace ORC\Util\File;
interface Validator {
	/**
	 * 
	 * @param FileInfo $fileinfo
	 * @throws \ORC\Util\File\Validator\Exception
	 * @return void
	 */
	public function valid(FileInfo $fileinfo);
	
	public function getError();
	
	public function getName();
}