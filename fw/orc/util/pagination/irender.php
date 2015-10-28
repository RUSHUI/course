<?php
namespace ORC\Util\Pagination;
use ORC\Util\Pagination;
interface IRender {
    public function __construct($action_name, array $params = array());
    
    public function toHtml(Pagination $pagination);
}