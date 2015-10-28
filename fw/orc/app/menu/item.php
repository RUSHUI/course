<?php
namespace ORC\APP\Menu;
class Item {
    /**
     * 
     * @var string the name of the item
     */
    protected $_name;
    /**
     * 
     * @var string url to the item
     */
    protected $_url;
    /**
     * 
     * @var bool whether the item is active
     */
    protected $_isActive;
    /**
     * 
     * @var string the link title, shows in the tip hint
     */
    protected $_title;
    
	/**
     * @return the $name
     */
    public function getName()
    {
        return $this->_name;
    }

	/**
     * @return the $url
     */
    public function getUrl()
    {
        return $this->_url;
    }

	/**
     * @return the $isActive
     */
    public function isActive()
    {
        return $this->_isActive;
    }

	/**
     * @return the $title
     */
    public function getTitle()
    {
        return $this->_title;
    }

	/**
     * @param string $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

	/**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->_url = $url;
    }

	/**
     * @param boolean $isActive
     */
    public function setActive($isActive)
    {
        $this->_isActive = $isActive;
    }

	/**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->_title = $title;
    }
}