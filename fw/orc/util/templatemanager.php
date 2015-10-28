<?php
namespace ORC\Util;
use ORC\Exception\TemplateNotFoundException;
use ORC\Exception\SystemException;
class TemplateManager {
    const DEFAULT_TEMPLATE = 'default';
    private static $_instance;
    /**
     *
     * @return \ORC\Util\TemplateManager
     */
    public static function getInstance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * 
     * @var \ORC\Util\Template
     */
    private $_templates = array();
    
    private $_wildcard_template_actions = array();
    /**
     * 
     * @var string
     */
    private $_template_actions = array();
    private function __construct() {
        //@todo add cache
        $dir = new \DirectoryIterator(DIR_APP_TEMPLATE_ROOT);
        foreach ($dir as $fileInfo) {
            if ($fileInfo->isDot()) continue;
            if ($fileInfo->getExtension() == 'yml') {
                $template_name = $fileInfo->getBasename('.yml');
                $this->parseTemplate($template_name);
            }
        }
        $this->parseTemplateActions();
    }
    
    /**
     * 
     * @param string $template_name
     * @return \ORC\Util\Template
     * @throws TemplateNotFoundException
     */
    public function getTemplate($template_name) {
        $template_name = strtolower($template_name);
        if (isset($this->_templates[$template_name])) {
            return $this->_templates[$template_name];
        }
        throw new TemplateNotFoundException('template not found!');
    }
    
    /**
     * find the template name for certain action
     * @param \ORC\MVC\Action $action
     * @return \ORC\Util\Template
     * @throws TemplateNotFoundException
     */
    public function findTemplate(\ORC\MVC\Action $action) {
        if (!($template_name = $action->getTemplateName())) {
            $action_name = $action->getName();
            $action_name = strtolower($action_name);
            if (isset($this->_template_actions[$action_name])) {
                $template_name = $this->_template_actions[$action_name];
            }
            if (!$template_name) {
                //search the wildcard actions
                //@todo maybe need rewrite the algorithm
                $action_args = explode('.', $action_name);
                foreach ($this->_wildcard_template_actions as $k => $v) {
                    $args = explode('.', $k);
                    $found = false;
                    $arg_length = count($args);
                    if ($arg_length > count($action_args)) {
                        continue;
                    }
                    foreach ($action_args as $index => $tmp) {
                        //pre('check index ' . $index);
                        if ($args[$index] != '*' && $args[$index] != $tmp) {
                            break;
                        }
                        if ($args[$index] == $tmp && ($index != ($arg_length - 1))) {
                            //same but not the last
                            //pre('same!');
                            continue;
                        }
                        if ($args[$index] == '*') {
                            if ($index == ($arg_length -1)) {
                                //last *
                                //e.g. admin.* will match admin.xxx and admin.xxx.yyy
                                $found = true;
                            } else {
                                //* will match any arg
                                //pre('* found!');
                                continue;
                            }
                        }
                        if ($index == ($arg_length -1)) {
                            //reach the last one
                            //pre('find last');
                            $found = true;
                            break;
                        }
                    }
                    if ($found) {
                        if ($template_name) {
                            throw new SystemException('action match found in ' . $v . ', previously defined in ' . $template_name);
                        }
                        $template_name = $v;
                    }
                }
            }
            if (!$template_name) {
                $template_name = self::DEFAULT_TEMPLATE;
            }
        }
        return $this->getTemplate($template_name);
    }
    
    private function parseTemplate($template_name) {
        $template = new Template($template_name);
        $this->_templates[$template_name] = $template;
    }
    
    private function parseTemplateActions() {
        foreach ($this->_templates as $template_name => $template) {
            if ($template_name == self::DEFAULT_TEMPLATE) continue;
            $actions = $template->getActions();
            if (is_array($actions)) {
                foreach ($actions as $action) {
                    $action = strtolower($action);
                    if (strpos($action, '*') !== false) {
                        if (isset($this->_wildcard_template_actions[$action])) {
                            throw new SystemException('duplicated wildcard action found in ' . $template_name. ', previously defined in ' . $this->_wildcard_template_actions[$action]);
                        }
                        $this->_wildcard_template_actions[$action] = $template_name;
                        continue;
                    }
                    if (isset($this->_template_actions[$action])) {
                        throw new SystemException('duplicated action found in ' . $template_name. ', previously defined in ' . $this->_template_actions[$action]);
                    }
                    $this->_template_actions[$action] = $template_name;
                }
            }
        }
    }
}