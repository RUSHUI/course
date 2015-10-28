<?php
use ORC\MVC\Action;
class Example_Demo_Action extends Action {
    public function execute() {
        $model = $this->getModel('Example.Demo');
        $model->UserDemo();
        return $this->HTMLView('Example.Why');
    }
}
?>