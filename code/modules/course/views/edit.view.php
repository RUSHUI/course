<?php
use ORC\MVC\View;
class Course_Edit_View extends View{
    public function execute(){
        // $this->addCss('/css/courses/courses.css');
        // $this->addJs('/js/courses/courses.js');
        $this->renderTemplate('Course.Edit');
    }
}