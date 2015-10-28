<?php
namespace ORC\Util;
use ORC\APP\Session;
/**
 * 用来将一些消息存在session里
 * 典型的应用场景是在上一个动作时，一些提示信息被产生，然后页面跳转到下一个页面的时候显示这些信息
 * 例如保存后自动跳转
 * @author zhouyanqin
 *
 */
class MessagePool {
    const TYPE_INFO = 'info';
    const TYPE_WARNING = 'warning';
    const TYPE_ERROR = 'danger';
    const TYPE_SUCCESS = 'success';
    
    const SESSION_KEY = '__messages__';
    private static $instance;
    /**
     * 
     * @return \ORC\Util\MessagePool
     */
    public static function getInstance() {
        if (isset(self::$instance)) {
            return self::$instance;
        }
        self::$instance = new self();
        return self::$instance;
    }
    
    private $session;
    
    private function __construct() {
        $this->session = Session::getInstance();
    }
    
    public function push($message, $type = self::TYPE_INFO) {
        $messages = $this->_getMessages();
        $messages[] = array('msg' => $message, 'type' => $type);
        $this->_storeMessages($messages);
        return true;
    }
    
    public function unshift($message, $type = self::TYPE_INFO) {
        $messages = $this->_getMessages();
        array_unshift($messages, array('msg' => $message, 'type' => $type));
        $this->_storeMessages($messages);
        return true;
    }
    
    public function getMessages($autoClear = true) {
        $messages = $this->_getMessages();
        if ($autoClear) {
            $this->clearMessages();
        }
        return $messages;
    }
    
    public function clearMessages() {
        $this->_storeMessages(array());
    }
    
    public function getMessagesCount() {
        return count($this->_getMessages());
    }
    
    private function _getMessages() {
        $messages = $this->session->get(self::SESSION_KEY);
        if (empty($messages)) {
            $messages = array();
        }
        return $messages;
    }
    
    private function _storeMessages(array $messages) {
        return $this->session->set(self::SESSION_KEY, $messages);
    }
}