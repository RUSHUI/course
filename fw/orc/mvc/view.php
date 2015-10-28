<?php
namespace ORC\MVC;
use ORC\Core\Config;
abstract class View {
    /**
     *
     * @var \ORC\MVC\Controller
     */
    protected $_controller;
    public function __construct(Controller $c) {
        $this->_controller = $c;
    }
    
    public function render() {
        ob_start();
        $this->execute();
        return ob_get_clean();
    }
    
    /**
     * display the final code want to show
     */
    abstract public function execute();
    
    public function getName() {
        $class_name = strtolower(get_class($this));
        $class_name = preg_replace('/(_view)$/', '', $class_name);
        $action_name = str_replace('_', '.', $class_name);
        return $action_name;
    }
    
    protected function getController() {
        return $this->_controller;
    }
    
    protected function getRequest() {
        return $this->getController()->getRequest();
    }
    
    protected function getResponse() {
        return $this->getController()->getResponse();
    }
    
    protected function renderTemplate($template_name) {
        $viewModel = $this->getViewModel();
        $modelData = $viewModel->getAllModelData();
        $request = \ORC\MVC\Request::getInstance();
        $template_file = $this->_controller->getFilePath('template', $template_name);
        if (Config::getInstance()->get('template.debug')) {
            echo sprintf('<!-- using content template %s begin -->', $template_name);
        }
        include $template_file;
        if (Config::getInstance()->get('template.debug')) {
            echo sprintf('<!-- using content template %s end -->', $template_name);
        }
    }
    
    protected function includeTemplate($template_name_c0c4648d020076fcb9f7ef1460db10b0) {
        $args = func_get_args();
        array_shift($args);
        $data_5656d190508c77a079411fbbb437745f = array();
        foreach ($args as $arg) {
            if (is_array($arg)) {
                foreach ($arg as $k => $v) {
                    $data_5656d190508c77a079411fbbb437745f[$k] = $v;
                }
            }
        }
        $template_filename_c0c4648d020076fcb9f7ef1460db10b0 = $this->_controller->getFilePath('template', $template_name_c0c4648d020076fcb9f7ef1460db10b0);
        unset($args, $arg, $k, $v);
        $request = \ORC\MVC\Request::getInstance();
        extract($data_5656d190508c77a079411fbbb437745f);
        unset($data_5656d190508c77a079411fbbb437745f);
//         $viewModel = $this->getViewModel();
//         $modelData = $viewModel->getAllModelData();
//         $request = \ORC\MVC\Request::getInstance();
        if (Config::getInstance()->get('debug.template')) {
            echo sprintf('<!-- using content template %s begin -->', $template_name_c0c4648d020076fcb9f7ef1460db10b0);
        }
        include $template_filename_c0c4648d020076fcb9f7ef1460db10b0;
        if (Config::getInstance()->get('debug.template')) {
            echo sprintf('<!-- using content template %s end -->', $template_name_c0c4648d020076fcb9f7ef1460db10b0);
        }
    }
    
    protected function generateURL($action_name = null, array $params = array()) {
        return $this->_controller->generateURL($action_name, $params);
    }
    
    protected function getModelData($model_name) {
        $model = $this->_controller->getModel($model_name);
        return $model->getAllData();
    }
    
    protected function addCss($path, $media = 'all') {
        $this->getViewModel()->addCss($path, $media);
    }
    
    protected function addJs($path) {
        $this->getViewModel()->addJs($path);
    }
    
    protected function addRawJs($code) {
        $this->getViewModel()->addRawJs($code);
    }
    
    protected function addRawCss($code) {
        $this->getViewModel()->addRawCss($code);
    }
    
    protected function setPageTitle($title) {
        $this->getViewModel()->setTitle($title);
    }
    /**
     * want to execute the block code, but do not want to show in the current template
     * @param string $block_name
     */
    public function hideBlock($block_name) {
        $this->getController()->getAction()->hideBlock($block_name);
    }
    
    /**
     * totally ignore the block, will neither execute code nor show it
     * @param string $block_name
     */
    public function ignoreBlock($block_name) {
        $this->getController()->getAction()->ignoreBlock($block_name);
    }
    
    /**
     * this function can add some speical item to certain block
     * The item will be add to the end of the block
     * @param string $block_name
     * @param string $name
     */
    public function addToBlock($block_name, $name) {
        $this->getController()->getAction()->addToBlock($block_name, $name);
    }
    
    public function useTemplate($template_name) {
        $this->getController()->getAction()->useTemplate($template_name);
    }
    
    protected function getViewModel() {
        return ViewModel::getInstance();
    }
}