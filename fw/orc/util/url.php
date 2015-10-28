<?php
namespace ORC\Util;
use ORC\Exception\NotExistsException;
use ORC\Application;
use ORC\Core\Config;
class Url {
    public static function getFullHttpPath($path) {
        if (substr($path, 0, 4) == 'http') {
            return $path;
        }
        return Config::getInstance()->get('main_server') . ltrim($path, '/');
    }
    
    /**
     * 
     * @param string $action_name null if use default
     * @param array $params
     * @throws NotExistsException
     * @return string
     */
    public static function generateURL($action_name = null, array $params = array()) {
        $default_action = Application::getApp()->getDefaultAction();
        if ($action_name == null) {
            $action_name = $default_action;
        }
        $route = Route::getInstance();
        $routes = $route->get('routes');
        $path = array_search(strtolower($action_name), $routes);
        if (!$path) {
            throw new NotExistsException('Can not find the path', $action_name);
        }
        $main_server = Config::getInstance()->get('main_server');
        if (strcasecmp($default_action, $action_name) == 0 && empty($params)) {
            if (empty($params)) {
                return $main_server;
            } else {
                $path = '';
            }
        }
        $base = $path;
        $p = array();
        foreach ($params as $key => $value) {
            if (is_int($key)) {
                $base .= '/' . $value;
            } else {
                $p[] = urlencode($key) . '=' . urlencode($value);
            }
        }
        $base = rtrim($base, '/');
        if ($base) {
            if (Config::getInstance()->get('app.seo.ext')) {
                $base .= '.' . Config::getInstance()->get('app.seo.ext');
            }
        }
        if (count($p)) {
            $base .= '?' . implode('&', $p);
        }
        return $main_server . ltrim($base, '/');
    }
    
    public static function getCurrentURL($full_url = true) {
        $url = $_SERVER['REQUEST_URI'];
        if ($full_url) {
            $pathes = parse_url(Config::getInstance()->get('main_server'));
            $domain = sprintf('%s://%s', $pathes['scheme'], $pathes['host']);
            $url = $domain . $url;
        }
        return $url;
    }
    
    public static function tryRelativePath() {
        $args = func_get_args();
        foreach ($args as $arg) {
            $urls = parse_url($arg);
            if (isset($urls['scheme'])) {
                return $arg;
            }
            if (empty($urls['path']) || $urls['path'] == '/') continue;
            $filepath = DIR_APP_PUBLIC . '/' . ltrim($urls['path'], '/');
            if (file_exists($filepath)) {
                return $arg;
            }
        }
        return '';
    }
}