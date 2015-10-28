<?php
namespace ORC\MVC;
use ORC\Exception\ViewNotFoundException;
use ORC\Util\Logger;
use ORC\Exception\SystemException;
use ORC\Util\CaseContainer;
/**
 * @author 彦钦
 *
 */
class Response {
    /**
     * @var int
     */
    protected $viewType;
    /**
     * @var string
     */
    protected $viewName;
    /**
     * 
     * @var \ORC\MVC\Response\Base
     */
    protected $render;
    
    protected $controller;
    
    protected $_redirect_url;
    
    protected $_headers;
    
    protected $_code;
    
    public function __construct(Controller $c) {
        $this->controller = $c;
        $this->_headers = new CaseContainer();
    }
    
    public function setRedirect($url) {
        $this->_redirect_url = $url;
    }
    
    public function render(View $view) {
        switch ($this->getViewType()) {
            case Action::VIEW_HTML:
                $this->render = new \ORC\MVC\Response\HTML($this->controller);
                break;
            case Action::VIEW_JSON:
                $this->render = new \ORC\MVC\Response\JSON($this->controller);
                break;
            case Action::VIEW_RAW:
                $this->render = new \ORC\MVC\Response\RAW($this->controller);
                break;
            case Action::VIEW_REDIRECT:
                //redirect already be handled in controller, so here should not be this option
                //break;
            default:
                throw new ViewNotFoundException('unknown view type from action');
                break;
        }
        ViewModel::getInstance()->registerAll();//load all data from model
        $this->content = $view->render();
        return $this->render->render();
    }
    
    /**
     * set the response code
     * @param int $code
     */
    public function setCode($code) {
        $this->_code = (int)$code;
    }
    
    public function getCode() {
        return $this->_code;
    }
    
    public function redirect() {
        while (@ob_end_clean());
        header('Location: ' . $this->_redirect_url);
        exit();
    }
    
    /**
     * clear all headers
     */
    public function clearHeader() {
        if ($this->headersSent()) {
            throw new SystemException('Headers already sent!');
        }
        $this->_headers->removeAll();
        return true;
    }
    
    public function addHeader($header, $replace = true) {
        if ($this->headersSent()) {
            throw new SystemException('Headers already sent!');
        }
        list($name, $value) = explode(':', $header, 2);
        $value = trim($value);
        if($replace) {
            $this->_headers->set($name, array($value));
        } else {
            $this->_headers->append($name, $value);
        }
        return true;
    }
    
    public function removeHeader($header) {
        if ($this->headersSent()) {
            throw new SystemException('Headers already sent!');
        }
        list($name, $value) = explode(':', $header, 2);
        $value = trim($value);
        $values = $this->_headers->get($name);
        if (is_array($values)) {
            $found = false;
            foreach ($values as $k => $v) {
                if (strcasecmp($v, $value) == 0) {
                    //unset the values
                    $found = true;
                    unset($values[$k]);
                    break;
                }
            }
            if ($found) {
                if (count($values)) {
                    $this->_headers->set($name, $values);
                } else {
                    $this->_headers->remove($name);
                }
            }
        }
        return true;
    }
    
    public function sendHeaders() {
        if ($this->headersSent()) {
            throw new SystemException('Headers already sent!');
        }
        //first remove all header, maybe not be able to remove because of cookie problem
        //header_remove();
        $headers = $this->_headers->getAllData();
        foreach ($headers as $name => $values) {
            foreach ($values as $v) {
                header(sprintf('%s: %s', $name, $v));
            }
        }
    }
    
    public function headersSent() {
        $result = headers_sent($filename, $line);
        if ($result) {
            //log the filename and line
            $logger = Logger::getInstance('header');
            $logger->addNotice(sprintf('Headers already sent in %s on line %d', $filename, $line));
        }
        return $result;
    }
    
    /**
     * to display a html page and redirect to url
     * @param \ORC\APP\Response\Redirect $redirect null if want to get the redirect
     * @return \ORC\APP\Response\Redirect
     */
    public function HTMLRedirect(\ORC\APP\Response\Redirect $redirect = null) {
        static $obj;
        //use static value here to make sure that all response instances share the same value
        if ($redirect) {
            $obj = $redirect;
        }
        return $obj;
    }
    
    public function setContent($content) {
        $this->content = $content;
    }
    
    /**
     * get the main content part
     * used for $content in main template
     * @return string
     */
    public function getContent() {
        return $this->content;
    }
    
    
    /**
     * 
     * @return \ORC\MVC\Response\Base
     */
    public function getRender() {
        return $this->render;
    }
    /**
     * @return the $viewType
     */
    public function getViewType()
    {
        return $this->viewType;
    }
    
    /**
     * @return the $viewName
     */
    public function getViewName()
    {
        return $this->viewName;
    }
    
    /**
     * @param number $viewType
     */
    public function setViewType($viewType)
    {
        $this->viewType = $viewType;
    }
    
    /**
     * @param string $viewName
     */
    public function setViewName($viewName)
    {
        $this->viewName = $viewName;
    }
}