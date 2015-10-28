<?php
namespace ORC\MVC\Response;
class JSON extends Base {
	
    public function render()
    {
        $content = $this->_controller->getResponse()->getContent();
        if (is_array($content)) {
            return json_encode($content);
        }
        if (null !== json_decode($content, true)) {
            return $content;
        }
        //not json
    }

    
}