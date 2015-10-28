<?php
namespace ORC\API\Interior\Client;
use ORC\Core\Config;
use ORC\Exception\ConfigException;
use ORC\API\Interior\Exception\Exception;
class Connection {
    protected $url;
    protected $isPost = true;
    
    public function __construct($config_name) {
        $config = Config::getInstance();
        $url = $config->get('interior.' . $config_name. '.url');
        if ($url) {
            $this->url = $url;
        } else {
            throw new ConfigException('Wrong interior server config for ' . $config_name);
        }
    }
    
    public function getUrl() {
        return $this->url;
    }
    
    public function usePost($post = null) {
        if ($post === null) {
            return $this->isPost;
        }
        return $this->isPost = (bool) $post;
    }
    
    public function getRawResponse($action, array $params = array(), $gzip = false) {
        $url = $this->buildUrl($action, $params, $gzip);
//         pre($url);
        $result = $this->doRequest($url, $params);
//         pre(strlen($result));
        return new Response($result, $gzip, $url);
    }
    
    /**
     * 
     * @param unknown $action
     * @param array $params
     * @param string $gzip
     * @throws \ORC\API\Interior\Exception\Exception
     */
    public function getData($action, array $params = array(), $gzip = false) {
        $response = $this->getRawResponse($action, $params, $gzip);
        if ($response->hasError()) {
            throw new Exception($response->getMessage(), $response->getCode(), $response);
        }
        return $response->getData();
    }
    
    /**
     * 拼成最终请求的url
     * 如果连接的是非ORC框架Interior Server，可以通过继承这个类覆写这个方法的方法来达到目的
     * @param string $action
     * @param array $param
     * @param bool $gzip
     */
    protected function buildUrl($action, array $params, $gzip) {
        $url = $this->getUrl();
        //加上action
        //检测?或者#
        $url_fragment = null;
        if ($action) {
            $action = str_replace('.', '/', $action);
            $url_info = parse_url($url);
            if ((!empty($url_info['query'])) || (!empty($url_info['fragment']))) {
                $url_info['path'] = rtrim($url_info['path'], '/') . '/' . strtolower($action);
                if (!empty($url_info['fragment'])) {
                    $url_fragment = $url_info['fragment'];
                    unset($url_info['fragment']);
                }
                $url = $this->unparse_url($url_info);
            } else {
                $url = rtrim($url, '/') . '/' . strtolower($action);
            }
        }
        if ($this->usePost()) {
            //param就不需要加到url里面
            if ($gzip) {
                if (strpos($url, '?') !== false) {
                    $url .= '&gzip=1';
                } else {
                    $url .= '?gzip=1';
                }
            }
        } else {
            //将param放到url中
            //不使用http_build_query，防止urlencode
            $extraurl = array();
            if ($gzip) {
                $extraurl[] = 'gzip=1';
            }
            foreach ($params as $key => $value) {
                $extraurl[] = sprintf('%s=%s', $key, $value);
            }
            if (count($extraurl)) {
                $extraurl = implode('&', $extraurl);
                if (strpos($url, '?') !== false) {
                    $url .= '&' . $extraurl;
                } else {
                    $url .= '?' . $extraurl;
                }
            }
        }
        if ($url_fragment) { //保证锚点在url的最后
            $url .= '#' . $url_fragment;
        }
        return $url;
    }
    
    protected function doRequest($url, array $params = array()) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($this->usePost()) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        } else {
            //使用get，什么也不干
        }
        curl_setopt($ch, CURLOPT_HEADER, false);
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        if ($info['http_code'] == 403) {
            curl_close($ch);
            throw new Exception('禁止连接!', Exception::CODE_FORBIDDEN, $info);
        }
        if ($info['http_code'] == 404) {
            curl_close($ch);
            throw new Exception('找不到资源', Exception::CODE_NOT_FOUND, $info);
        }
        //         pre($result);
        if ($result === false) {
            $error = sprintf('%s: %s', curl_errno($ch), curl_error($ch));
            curl_close($ch);
            throw new Exception('连接后台服务器失败', Exception::CODE_SYSTEM_ERROR, $error);
        }
        curl_close($ch);
        return $result;
    }

    /**
     * copied from PHP manual function parse_url
     * @param array $parsed_url
     * @return string
     */
    protected function unparse_url(array $parsed_url)
    {
        $scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass = isset($parsed_url['pass']) ? ':' . $parsed_url['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    } 
}