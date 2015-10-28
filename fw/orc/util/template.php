<?php
namespace ORC\Util;
use ORC\Exception\TemplateNotFoundException;
use Symfony\Component\Yaml\Yaml;
use ORC\Util\Template\Block;
use ORC\Util\Template\Item\Item;
use ORC\Util\Template\Content;
use ORC\Application;
use ORC\Exception\TemplateException;
use ORC\Core\Config;
class Template {
    const CONTENT_BLOCK = 'content';
    const RESERVED_NAMES = 'js,css,raw_js,raw_css,title';
    private $_blocks = array();
    private $_template_name;
    private $_actions = array();
    private $_variables = array();
    private $_jses = array();
    private $_csses = array();
    private $_tpl_file;
    private $_yml_file;
    
    private $_outputs;
    
    private $_names = array();
    /**
     * 
     * @var \ORC\MVC\ViewModel
     */
    private $_viewModel;
    
    public function __construct($template_name) {
        //load default js and css
        $config = Config::getInstance();
        if ($config->exists('template.js')) {
            $this->_jses = $config->get('template.js');
        }
        if ($config->exists('template.css')) {
            $csses = $config->get('template.css');
            foreach ($csses as $media => $list) {
                foreach ($list as $css) {
                    $this->_csses[] = array('path' => $css, 'media' => $media);
                }
            }
        }
        $this->_template_name = strtolower($template_name);
        $this->parseTemplate($this->_template_name);
    }
    
    public function getName() {
        return $this->_template_name;
    }
    
    public function getActions() {
        return $this->_actions;
    }
    
    public function getTemplateFile() {
        return $this->_tpl_file;
    }
    
    public function getYmlFile() {
        return $this->_yml_file;
    }
    
    public function setViewModel(\ORC\MVC\ViewModel $viewModel) {
        $this->_viewModel = $viewModel;
        return $this;
    }
    
    public function getViewModel() {
        if (!isset($this->_viewModel)) {
            $this->_viewModel = \ORC\MVC\ViewModel::getInstance();
        }
        return $this->_viewModel;
    }
    
    public function render() {
        $outputs = array();
        $action = $this->getController()->getAction();
        $extra_items = $action->getExtraBlockItems();
        if (count($extra_items)) {
            foreach ($extra_items as $block_name => $v) {
                if ($block_name == self::CONTENT_BLOCK) {
                    throw new TemplateException('You can not use content as block name when using addToBlock');
                }
                if (!isset($this->_blocks[$block_name])) {
                    $this->_blocks[$block_name] = new \ORC\Util\Template\Block($v);
                } else {
                    foreach ($v as $vv) {
                        $this->_blocks[$block_name]->addItem($vv);
                    }
                }
            }
        }
        $ignore_blocks = $action->getIgnoreBlocks();
        $hidden_blocks = $action->getHiddenBlocks();
        foreach ($this->_blocks as $block_name => $block) {
            if (in_array($block_name, $ignore_blocks)) {
                continue;
            }
            if ($block_name == self::CONTENT_BLOCK) {
                $outputs[$block_name] = implode("\n", $this->prepareContent($block));
            } else {
                $outputs[$block_name] = implode("\n", $this->prepareBlock($block));
            }
            if (in_array($block_name, $hidden_blocks)) {
                unset($outputs[$block_name]);
            }
        }
        $reserved_names = explode(',', self::RESERVED_NAMES);
        foreach ($reserved_names as $name) {
            $outputs[$name] = $this->prepareReservedVariable($name);
        }
        foreach ($this->_variables as $name => $value) {
            $outputs[$name] = $this->prepareVariable($name, $value['default']);
        }
        unset($block_name, $block, $reserved_names);
        extract($outputs);
        ob_start();
        include $this->_tpl_file;
        if(Config::getInstance()->get('template.debug')) {
            echo sprintf('<!-- Using template %s -->', $this->_template_name);
        }
        return ob_get_clean();
    }
    
    protected function generateURL($action_name = null, array $params = array()) {
        return $this->getController()->generateURL($action_name, $params);
    }
    
    /**
     * get the html output ready for block
     * @param string $block_name
     * @return array
     */
    private function prepareBlock(Block $block) {
        $items = $block->getItems();
        $outputs = array();
        foreach ($items as $item) {
            $outputs[] = $this->renderItem($item);
        }
        return $outputs;
    }
    
    /**
     * get the html output for main content
     * @param Content $content
     * @return array
     */
    private function prepareContent(Content $content) {
        $outputs = array();
        $before = $content->getBefore();
        foreach ($before as $item) {
            $outputs[] = $this->renderItem($item);
        }
        //render the content
        $outputs[] = $this->getController()->getResponse()->getContent();
        $after = $content->getAfter();
        foreach ($after as $item) {
            $outputs[] = $this->renderItem($item);
        }
        return $outputs;
    }
    
    /**
     * 
     * @param string $name
     * @return string
     */
    private function prepareVariable($name, $default_value = '') {
        $value = $this->getViewModel()->get($name);
        if (null === $value) {
            $value = $default_value;
        }
        return $value;
    }
    
    private function prepareReservedVariable($name) {
        $output = '';
        $cache_buster = Config::getInstance()->get('template.cache_buster');
        if ($cache_buster == '') {
            $cache_buster = \ORC\Util\Util::getNow();
        }
        switch($name) {
            case 'js':
                //first get the template js
                $existing_jses = array();
                $jses = array_merge($this->_jses, $this->getViewModel()->getAllJs());
                //pre($jses, $this->_jses);
                foreach ($jses as $path) {
                    if (in_array($path, $existing_jses)) continue;
                    $existing_jses[] = $path;
                    $path = Url::getFullHttpPath($path);
                    $output .= sprintf('<script type="text/javascript" src="%s?%s"></script>', $path, $cache_buster);
                }
                break;
            case 'css':
                $existing_csses = array();
                $csses = array_merge($this->_csses, $this->getViewModel()->getAllCss());
                foreach ($csses as $css) {
                    if (!isset($existing_csses[$css['media']])) {
                        $existing_csses[$css['media']] = array();
                    }
                    if (in_array($css['path'], $existing_csses[$css['media']])) continue;
                    $existing_csses[$css['media']][] = $css['path'];
                    $css['path'] = Url::getFullHttpPath($css['path']);
                    $output .= sprintf('<link type="text/css" rel="stylesheet" href="%s?%s" media="%s" />', $css['path'], $cache_buster, $css['media']);
                }
                break;
            case 'raw_js':
                $output .= implode("\n", $this->_viewModel->getRawJs());
                break;
            case 'raw_css':
                $output .= implode("\n", $this->_viewModel->getRawCss());
                break;
            case 'title':
                $output .= $this->getViewModel()->getTitle();
                break;
        }
        return $output;
    }
    
    private function renderItem(Item $item) {
        switch ($item->getType()) {
            case 'tpl':
                //@todo extract values
                ob_start();
                include $item->getFilePath();
                return ob_get_clean();
                break;
            case 'block':
                return $item->render();
                break;
        }
    }
    
    private function parseTemplate($template_name) {
        $yml_file = DIR_APP_TEMPLATE_ROOT . DIRECTORY_SEPARATOR . $template_name . '.yml';
        $tpl_file = DIR_APP_TEMPLATE_ROOT . DIRECTORY_SEPARATOR . $template_name . '.php';
        if (!file_exists($yml_file) || !file_exists($tpl_file)) {
            throw new TemplateNotFoundException('template not found!');
        }
        $tpl_config = Yaml::parse($yml_file);
        //convert all values to lowercase
        array_walk_recursive($tpl_config, function(&$v, $k) {
            $v = strtolower($v);
        });
        if (isset($tpl_config['extends'])) {
            foreach ($tpl_config['extends'] as $extend_template_name) {
                $this->parseTemplate($extend_template_name);
            }
        }
        $this->_yml_file = $yml_file;
        $this->_tpl_file = $tpl_file;
        $this->_actions = array();//action will not extends
        $reserved_names = explode(',', self::RESERVED_NAMES);
        if (isset($tpl_config['blocks'])) {
            $blocks = $tpl_config['blocks'];
            if(is_array($blocks)) { 
                foreach ($blocks as $block_name => $items) {
                    if (!is_array($items)) {
                        continue;
                    }
                    if (in_array($block_name, $reserved_names)) {
                        throw new TemplateException('You can not use reserved name for block name', $block_name);
                    }
                    if (isset($this->_names[$block_name])) {
                        throw new TemplateException('Name already taken.', $this->_names[$block_name]);
                    }
                    $this->_names[$block_name] = array('block', $template_name);
                    if ($block_name == self::CONTENT_BLOCK) {
                        if (isset($items['before'])) {
                            $content_before = $items['before'];
                        } else {
                            $content_before = array();
                        }
                        if (isset($items['after'])) {
                            $content_after = $items['after'];
                        } else {
                            $content_after = array();
                        }
                        $this->_blocks[self::CONTENT_BLOCK] = new \ORC\Util\Template\Content($content_before, $content_after);
                    } else {
                        $this->_blocks[$block_name] = new \ORC\Util\Template\Block($items);
                    }
                }
            }
        }
        if (!isset($this->_blocks[self::CONTENT_BLOCK])) {
            $this->_blocks[self::CONTENT_BLOCK] = new \ORC\Util\Template\Content(array(), array());
        }
        if (isset($tpl_config['actions'])) {
            $this->_actions = $tpl_config['actions'];
        }
        if (isset($tpl_config['variables'])) {
            $variables = $tpl_config['variables'];
            foreach ($variables as $var_name => $default_value) {
                if (in_array($var_name, $reserved_names)) {
                    throw new TemplateException('You can not use reserved name for variable name', $block_name);
                }
                if (isset($this->_names[$var_name])) {
                    throw new TemplateException('Name already taken.', $this->_names[$var_name]);
                }
                $this->_names[$var_name] = array('variable', $template_name);
                $this->_variables[$var_name] = array('default' => $default_value);
            }
        }
        if (isset($tpl_config['css'])) {
            foreach ($tpl_config['css'] as $media => $csses) {
                foreach ($csses as $css) {
                    $this->_csses[] = array('path' => $css, 'media' => $media);
                }
            }
        }
        if (isset($tpl_config['js'])) {
            foreach ($tpl_config['js'] as $js) {
                $this->_jses[] = $js;
            }
        }
//         $viewModel = ViewModel::getInstance();
//         if (isset($tpl_config['css'])) {
//             foreach ($tpl_config['css'] as $media => $csses) {
//                 foreach ($csses as $css) {
//                     $viewModel->addCss($css, $media);
//                 }
//             }
//         }
//         if (isset($tpl_config['js'])) {
//             foreach ($tpl_config['js'] as $js) {
//                 $viewModel->addJs($js);
//             }
//         }
    }
    
    private function getController() {
        return Application::getApp()->getController();
    }
}