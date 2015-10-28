<?php
namespace ORC\MVC;
use ORC\Core\Config;
abstract class Block extends ActionBlockBase {
	
	/**
	 * @return the final html code
	 */
	abstract public function render();
	
	protected function renderTemplate($template_name__5656d190508c77a079411fbbb437745f, array $data_5656d190508c77a079411fbbb437745f = array()) {
	    $args = func_get_args();
	    array_shift($args);
	    array_shift($args);
	    foreach ($args as $arg) {
	        if (is_array($arg)) {
    	        foreach ($arg as $k => $v) {
    	           $data_5656d190508c77a079411fbbb437745f[$k] = $v;
    	        }
    	    }
	    }
	    $template_filename_c0c4648d020076fcb9f7ef1460db10b0 = $this->_controller->getFilePath('template', $template_name__5656d190508c77a079411fbbb437745f);
	    unset($args, $k, $v);
	    ob_start();
	    extract($data_5656d190508c77a079411fbbb437745f);
	    unset($data_5656d190508c77a079411fbbb437745f);
	    if (Config::getInstance()->get('template.debug')) {
	        echo sprintf('<!-- using block template %s begin -->', $template_name__5656d190508c77a079411fbbb437745f);
	    }
	    include $template_filename_c0c4648d020076fcb9f7ef1460db10b0;
	    if (Config::getInstance()->get('template.debug')) {
	        echo sprintf('<!-- using block template %s end -->', $template_name__5656d190508c77a079411fbbb437745f);
	    }
	    return ob_get_clean();
	}
	
	protected function generateURL($action_name = null, array $params = array()) {
	    return $this->_controller->generateURL($action_name, $params);
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
	
	protected function getViewModel() {
	    return ViewModel::getInstance();
	}
}