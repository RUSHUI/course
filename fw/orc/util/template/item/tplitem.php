<?php
namespace ORC\Util\Template\Item;
class TplItem extends Common implements Item {
	/* (non-PHPdoc)
     * @see \ORC\Util\Template\Item\Item::getFilePath()
     */
    public function getFilePath()
    {
        $filepath = DIR_APP_MODULE_ROOT . DIRECTORY_SEPARATOR;
        $filepath .= $this->_module_name . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR;
        $filepath .= str_replace('.', DIRECTORY_SEPARATOR, $this->_extra) . '.template.php';
        return $filepath;
    }
}