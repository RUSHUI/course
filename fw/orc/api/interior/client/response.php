<?php
namespace ORC\API\Interior\Client;
class Response {
    
    const ERROR_CODE_OK = \ORC\API\Interior\Server\Response::ERROR_CODE_OK;
    const ERROR_CODE_SYSTEM = \ORC\API\Interior\Server\Response::ERROR_CODE_SYSTEM;
    
    /**
     * @var int
     */
    protected $code = self::ERROR_CODE_OK;
    /**
     * 
     * @var string
     */
    protected $message = '';
    /**
     * 
     * @var array
     */
    protected $data = array();
    
    protected $url;//the request url
    
    public function __construct($content, $gzip = false, $url = null) {
        $this->setContent($content, $gzip);
        $this->url = $url;
    }
 /**
     * @return the $code
     */
    public function getCode()
    {
        return $this->code;
    }

 /**
     * @return the $message
     */
    public function getMessage()
    {
        return $this->message;
    }

 /**
     * @return the $data
     */
    public function getData()
    {
        return $this->data;
    }

    public function getURL() {
        return $this->url;
    }
 /**
     * @param number $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

 /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

 /**
     * @param multitype: $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    public function hasError() {
        return $this->code !== self::ERROR_CODE_OK;
    }
    
    protected function setContent($content, $gzip) {
        if ($gzip) {
            $content = gzuncompress($content);
        }
        $data = @json_decode($content, true);
        if ($data === null) {
            $this->code = self::ERROR_CODE_SYSTEM;
            $this->message = '无法解析服务端消息';
            return false;
        }
        $this->code = $data['code'];
        $this->message = empty($data['message']) ? '' : $data['message'];
        if (isset($data['data']) && is_array($data['data'])) {
            $this->data = $data['data'];
        }
        return true;
    }
    
}