<?php
namespace ORC\API\Interior\Server;
interface IServer {
    /**
     * 向接口输出数据
     * @param array $data
     * @return \ORC\MVC\Response
     */
    public function sendContent(Response $response);
    
    /**
     * 检查是否有权限
     * @return bool
     */
    public function auth();
}