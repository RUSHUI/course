<?php
namespace ORC\MVC;
use ORC\APP\User;
abstract class ActionBlockBase {
    protected $_controller;
    public function __construct(\ORC\MVC\Controller $controller) {
        $this->_controller = $controller;
    }
    
    /**
     * 
     * @return \ORC\MVC\Controller
     */
    public function getController() {
        return $this->_controller;
    }
    
    protected function getModel($name) {
        return $this->_controller->getModel($name);
    }
    
    /**
     * get current user
     * @return \ORC\APP\User\IUser
     */
    protected function getMe() {
        return User::me();
    }
    
    /**
     * @return \ORC\MVC\Request
     */
    protected function getRequest() {
        return $this->_controller->getRequest();
    }
    
    protected function getReferURL() {
        $request = $this->getRequest();
        $url = $request->get('refer');
        if (!$url) {
            $url = $request->getReferURL();
        }
        if (!$url) {
            $url = @$_SERVER['HTTP_REFERER'];
        }
        return $url;
    }
}