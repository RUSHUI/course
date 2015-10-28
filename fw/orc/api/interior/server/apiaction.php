<?php
namespace ORC\API\Interior\Server;
use ORC\MVC\ActionBlockBase;
abstract class APIAction extends ActionBlockBase {
    
    abstract public function execute();
    
    /**
     * 
     * @param array $data
     * @param bool $gzip, if set to null, then use auto
     * @return \ORC\API\Interior\Server\Response
     */
    protected function send(array $data, $gzip = null) {
        if ($gzip === null) {
            $gzip = (boolean) $this->getRequest()->get('gzip');
        }
        $response = new Response();
        $response->setData($data);
        $response->setGzip($gzip);
        return $response;
    }
}