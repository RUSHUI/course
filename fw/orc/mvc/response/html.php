<?php
namespace ORC\MVC\Response;
use ORC\Util\TemplateManager;
use ORC\Core\Config;
use ORC\Util\AdvancedContainer;
use ORC\MVC\ViewModel;
class HTML extends Base {

    public function render()
    {
        //add default raw js
        $viewModel = ViewModel::getInstance();
        $js = 'function site_config(key) {return {"base_url":"' . Config::getInstance()->get('main_server') . '","window_id":' . mt_rand(1, 1000000) . '}[key.toLowerCase()];}';
        $viewModel->addRawJs($js);
        $action = $this->_controller->getAction();
        //find the correct template
        $template = TemplateManager::getInstance()->findTemplate($action);
        return $template->render();
    }

    protected function getLayoutConfig() {
        static $layout;
        if (!isset($layout)) {
            $config = Config::getInstance();
            $layout = new AdvancedContainer();
            foreach ($config->get('layout') as $key => $value) {
                $layout->set($key, $value);
            }
        }
        return $layout;
    }
    
}