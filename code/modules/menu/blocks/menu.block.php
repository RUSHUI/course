<?php
use ORC\MVC\Block;
class Menu_Menu_Block extends Block {
    public function render() {
        return $this->renderTemplate('Menu.Block.Menu');
    }
}