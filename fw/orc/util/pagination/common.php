<?php
namespace ORC\Util\Pagination;
abstract class Common {
    protected $_action_name;
    protected $_params;
    public function __construct($action_name, array $params = array()) {
        $this->_action_name = $action_name;
        if (isset($params['page'])) {
            unset($params['page']);
        }
        $this->_params = $params;
    }
}