<?php
class Index_LoginBlock_Block extends \ORC\MVC\Block {
  public function render() {
    $request = $this->getRequest();
    // $model = $this->getModel('Index.Index');
    return $this->renderTemplate('Index.Block.LoginBlock');
}
}
