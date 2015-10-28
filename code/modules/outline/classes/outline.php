<?php
namespace APP\Module\OutLine;
class OutLine {
    protected static $instance;
    /**
     * 
     * @return \APP\Module\OutLine\OutLine
     */
    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    const DATAROW_CLASS = '\APP\Module\OutLine\DataRow\Node';
    
    protected $_outlines;
    
    protected function __construct() {
        
    }
    
    public function clearCache() {
        
    }
    
    protected function initOutLines() {
        
    }
}