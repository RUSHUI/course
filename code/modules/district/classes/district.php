<?php
namespace APP\Module\District;
use ORC\DAO\Table\Manager;
class District extends Manager {
    const TABLENAME = 'districts';
        
    public function __construct() {
        parent::__construct(self::TABLENAME);
    }
}