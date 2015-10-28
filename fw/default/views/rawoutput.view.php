<?php
use ORC\MVC\View;
class Default_RawOutput_View extends View {
    public function execute() {
        echo $this->getController()->getResponse()->getContent();
    }
}