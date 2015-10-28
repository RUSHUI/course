<?php
namespace ORC\API\Interior\Server;
class Response {

    const ERROR_CODE_OK = \ORC\API\Interior\Exception\Exception::CODE_NONE_ERROR;
    const ERROR_CODE_SYSTEM = \ORC\API\Interior\Exception\Exception::CODE_SYSTEM_ERROR;
    
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
    
    /**
     * @var boolean
     */
    protected $gzip = false;
    
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

 /**
     * @return the $gzip
     */
    public function getGzip()
    {
        return $this->gzip;
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
     * 
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

 /**
     * @param boolean $gzip
     */
    public function setGzip($gzip)
    {
        $this->gzip = $gzip;
    }

    public function getContent() {
        $result = array();
        $result['code'] = $this->getCode();
        if ($result['code'] === self::ERROR_CODE_OK) {
            $result['data'] = $this->getData();
        } else {
            $result['message'] = $this->getMessage();
        }
        return json_encode($result);
    }
    
    
}