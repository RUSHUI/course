<?php
namespace ORC\APP\Response;
class Redirect {
    const TYPE_SUCCESS = 1;
    const TYPE_FAILURE = 2;
    const TYPE_WARNING = 3;
    const TYPE_ERROR = 4;
    protected $_url;
    protected $_title;
    protected $_message;
    protected $_type;
    
    public function __construct($url, $title, $message, $type) {
        $this->_url = $url;
        $this->_title = $title;
        $this->_message = $message;
        $this->_type = $type;
    }
    
	/**
     * @return the $url
     */
    public function getUrl()
    {
        return $this->_url;
    }

	/**
     * @return the $title
     */
    public function getTitle()
    {
        return $this->_title;
    }

	/**
     * @return the $message
     */
    public function getMessage()
    {
        return $this->_message;
    }

	/**
     * @return the $type
     */
    public function getType()
    {
        return $this->_type;
    }

    
    
}