<?php
namespace ORC\MVC;
use ORC\Util\Container;
use ORC\Application;
use ORC\Core\Config;
use ORC\Util\CaseContainer;
class ViewModel {
    private static $_instance;
    /**
     * 
     * @return \ORC\MVC\ViewModel
     */
    public static function getInstance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new self(Application::getApp()->getController());
        }
        return self::$_instance;
    }
    
    private $_controller;
    private $_request;
    private $_model_container;
    private $_extra_container;
    private $_csses = array();
    private $_jses = array();
    private $_raw_csses = array();
    private $_raw_jses = array();
    private $_title;
    
    public function __construct(Controller $c) {
        $this->_controller = $c;
        $this->_request = Request::getInstance();
        $this->_model_container = new CaseContainer();
        $this->_extra_container = new Container();
    }
    
    public function getModelData($model_name, $key) {
        $model_name = strtolower($model_name);
        $model = $this->_model_container->get($model_name);
        if ($model) {
            return $model->get($key);
        } else {
            return null;
        }
    }
    
    public function getAllModelData() {
        return $this->_model_container;
    }
    
    public function getRequest() {
        return $this->_request;
    }
    
    public function set($k, $v) {
        return $this->_extra_container->set($k, $v);
    }
    
    public function get($k) {
        return $this->_extra_container->get($k);
    }
    
    public function registerAll() {
        $models = $this->_controller->getModels();
        foreach ($models as $model) {
            $this->register($model);
        }
    }
    
    public function register(Model $model) {
        $class_name = strtolower(get_class($model));
        $class_name = preg_replace('/(_model)$/', '', $class_name);
        $class_name = str_replace('_', '.', $class_name);
        $this->_model_container->set($class_name, $model);
    }
    
    public function addCss($path, $media = 'all') {
        $this->_csses[] = array('path' => $path, 'media' => $media);
    }
    
    public function addJs($path) {
        $this->_jses[] = $path;
    }
    
    public function addRawJs($code) {
        $this->_raw_jses[] = $code;
    }
    
    public function addRawCss($code) {
        $this->_raw_csses[] = $code;
    }
    
    public function getAllCss() {
        return $this->_csses;
    }
    
    public function getAllJs() {
        return $this->_jses;
    }
    
    public function getRawCss() {
        return $this->_raw_csses;
    }
    
    public function getRawJs() {
        return $this->_raw_jses;
    }
    
    public function setTitle($title) {
        $this->_title = $title;
    }
    
    public function getTitle() {
        $title = '';
        if (isset($this->_title)) {
            $title = $this->_title . ' - ';
        }
        return $title . Config::getInstance()->get('template.title');
    }
}
