<?php
namespace ORC\API\Interior\Client;
interface IAction {
    /**
     * 从server中获得数据
     * @param string $url server地址
     * @param array $params 请求参数
     * @param bool $gzip 是否使用gzip压缩
     * @return array
     */
    public function getContent($url, array $params = array(), $gzip = false);
}