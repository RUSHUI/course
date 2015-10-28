<?php
namespace ORC\Util\File\Processer;
use ORC\Util\FileUpload;
use ORC\Util\File\FileInfo;
use ORC\Exception\Exception;
class Resize extends Common implements \ORC\Util\File\Processer {
    protected $_destSize;
    protected $_keepRatio;
    public function __construct(array $destSize, $keepRatio = true) {
        $this->_destSize = $destSize;
        $this->_keepRatio = $keepRatio;
    }
	/* (non-PHPdoc)
	 * @see \ORC\Util\File\Processer::handle()
	 */
	public function handle(FileUpload $uploader, FileInfo $fileinfo) {
		// TODO Auto-generated method stub
		$filename = $fileinfo->getPath();
		$realpath = $uploader->getFileBaseDir() . DIRECTORY_SEPARATOR . $filename;
		//resize the file
		list($o_width, $o_height,) = getimagesize($realpath);
		list($des_width, $des_height) = $this->getDestSize(array($o_width, $o_height));
		$destination = $this->getDestName($fileinfo, array($des_width, $des_height));
		$full_destination = $uploader->getFileBaseDir() . DIRECTORY_SEPARATOR . $destination;
		$source = imagecreatefromstring(file_get_contents($realpath));
	    if (function_exists('imagescale')) {
	        $dest = imagescale($source, $des_width, $des_height);
	        if ($dest) {
	            $result = true;
	        } else {
	            $result = false;
	        }
	    } else {
	        $dest = imagecreatetruecolor($des_width, $des_height);
	        $result = imagecopyresampled($dest, $source, 0, 0, 0, 0, $des_width, $des_height, $o_width, $o_height);
	    }
	    imagedestroy($source);
	    if ($result) {
	        switch (strtolower($fileinfo->getExtension())) {
	            case 'jpg':
	            case 'jpeg':
	            default:
	                $result = imagejpeg($dest, $full_destination);
	                break;
	            case 'png':
	                $result = imagepng($dest, $full_destination);
	                break;
	            case 'gif':
	                $result = imagegif($dest, $full_destination);
	                break;
	            case 'bmp':
	                throw new Exception('不支持bmp文件');
	                break;
	        }
	        imagedestroy($dest);
	        if ($result) {
	            $info = new FileInfo($fileinfo->getName(), filesize($full_destination), $destination, $fileinfo->getType());
	            return $info;
	        }
	        return false;
	    }
		return false;
	}

	/**
	 * 需要的话就verride 
	 * @param FileInfo $fileinfo
	 * @param array $destSize
	 */
    public function getDestName(FileInfo $fileinfo, array $destSize) {
        $info = pathinfo($fileinfo->getPath());
        if ($info['dirname'] && $info['dirname'] != '.') {
            $filename = sprintf('%s%s%s', $info['dirname'], DIRECTORY_SEPARATOR, $info['filename']);
        } else {
            $filename = $info['filename'];
        }
        return sprintf('%s_%dx%d.%s', $filename, $destSize[0], $destSize[1], $info['extension']);
    }
    
    protected function getDestSize(array $originSize) {
        if ($this->_keepRatio == false) {
            return $this->_destSize;
        }
        $o_width = $originSize[0];
        $o_height = $originSize[1];
        //根据长宽比计算目标比例
        if ($o_width / $o_height >= $this->_destSize[0] / $this->_destSize[1]) {
            //宽比较大
            $dest_width = $this->_destSize[0];
            $dest_height = $o_height * $dest_width / $o_width;
        } else {
            $dest_height = $this->_destSize[1];
            $dest_width = $o_width * $dest_height / $o_height;
        }
        return array($dest_width, $dest_height);
    }
}