<?php
namespace ORC\Util\Template\Item;
use ORC\Exception\BlockNotFoundException;
use ORC\Application;
use ORC\MVC\Block;
class BlockItem extends Common implements Item {
    private $_file_path;
	/* (non-PHPdoc)
     * @see \ORC\Util\Template\Item\Item::getFilePath()
     */
    public function getFilePath()
    {
        if (!isset($this->_file_path)) {
            $filename = DIR_APP_MODULE_ROOT . DIRECTORY_SEPARATOR . $this->_module_name . DIRECTORY_SEPARATOR . 'blocks' . DIRECTORY_SEPARATOR;
            $filename .= str_replace('.', DIRECTORY_SEPARATOR, $this->_extra) . '.block.php';
            if (file_exists($filename)) {
                $this->_file_path = $filename;
            } else {
                throw new BlockNotFoundException('file not exists', $filename);
            }
        }
        return $this->_file_path;
    }

    public function render() {
        $class_name = sprintf('%s_%s_Block', $this->_module_name, str_replace('.', '_', $this->_extra));
        if (!class_exists($class_name)) {
            require $this->getFilePath();
        }
        $obj = new $class_name(Application::getApp()->getController());
        if (!($obj instanceof Block)) {
            throw new BlockNotFoundException('block object wrong!');
        }
        return $obj->render();
    }
    
}