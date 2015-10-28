<?php
namespace ORC\Util\File;
use ORC\Util\FileUpload;
interface Processer {
	public function getReturnIndex();
	/**
	 * @return array
	 * @param FileInfo $fileinfo
	 */
	public function handle(FileUpload $uploader, FileInfo $fileinfo);
}