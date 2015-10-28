<?php
namespace ORC\API\Interior\Exception;
class Exception extends \ORC\Exception\Exception {
    const CODE_NONE_ERROR = 0;
    const CODE_SYSTEM_ERROR = 1;
    const CODE_FORBIDDEN = 403;
    const CODE_NOT_FOUND = 404;
    const CODE_ACTION_UNKNOWN = 1000;//未分类错误
    const CODE_ACTION_NOTFOUND = 1001;
    protected $code;
    public function __construct($message, $code, $debug = null) {
        $this->code = $code;
        parent::__construct($message, $debug);
    }
    
    protected function retrieveCode() {
        return $this->code;
    }
}