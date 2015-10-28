<?php
namespace ORC\API\Interior\Server;
use ORC\Core\Config;
use ORC\MVC\Request;
use ORC\Application;
use ORC\Util\Util;
use ORC\API\Interior\Exception\Exception;
use ORC\API\Interior\Server\Response;
/**
 * 
 * @author pal
 *
 */
class Server implements IServer  {
    protected $routes;
    protected $actionName;
    
    public function __construct() {
        //load configuration
        $config = Config::getInstance();
        $this->routes = $config->get('interior.routes');
    }
    
    public function run() {
        $this->auth();
        $this->parseURI();
        $actionName = $this->getActionName();
        if (!$actionName) {
            $this->sendNotFound();
            exit();
        }
        $controller = Application::getApp()->getController();
        list($module_name, $action_name) = $this->_parseName($actionName);
        $class_name = sprintf('%s_%s_Action', $module_name, str_replace('.', '_', $action_name));
        if (!class_exists($class_name)) {
            $filename = $controller->getFilePath('action', $actionName);
            include_once $filename;
        }
        if (!class_exists($class_name)) {
            $this->sendNotFound();
            exit();
        }
        $class = new $class_name($controller);
        if (!($class instanceof APIAction)) {
            $this->sendNotFound();
            exit();
        }
        try {
            $response = $class->execute();
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $code = $ex->getCode();
            $response = new Response();
            $response->setCode($code);
            $response->setMessage($message);
        } catch (\Exception $ex) {
            $response = new Response();
            $response->setCode(Response::ERROR_CODE_SYSTEM);
            $response->setMessage($ex->getMessage());
        }
        if ($response == null) {
            $response = new Response();
            $response->setCode(Exception::CODE_ACTION_NOTFOUND);
            $response->setMessage('未知的动作');
        }
        $this->sendContent($response);
        exit();
    }
    
    
    /* (non-PHPdoc)
     * @see \ORC\API\Interior\Server\IServer::auth()
     * @todo
     */
    public function auth()
    {
        // TODO Auto-generated method stub
        $ip = Util::getIP();
        $config = Config::getInstance();
        $ips = $config->get('interior.ip');
        foreach ($ips as $ip_range) {
            if (strpos($ip_range, ',')) {
                list($start_ip, $end_ip) = explode(',', $ip_range);
                if (Util::inValidIPRange($ip, $start_ip, $end_ip)) {
                    return true;
                }
            } else {
                if ($ip == $ip_range) {
                    return true;
                }
            }
        }
        return $this->sendAccessDined();
    }

    public function getActionName() {
        return $this->actionName;
    }
    
 /* (non-PHPdoc)
     * @see \ORC\API\Interior\Server\IServer::sendContent()
     */
    public function sendContent(Response $response)
    {
        //clear the exiting content
        while (@ob_end_clean());
        if ($response->getGzip()) {
            $output = gzcompress($response->getContent());
        } else {
            $output = $response->getContent();
        }
        echo $output;exit();
    }
    
    protected function sendNotFound() {
        while (@ob_end_clean());
        header('HTTP/1.1 404 Not Found');
        exit();
    }
    
    protected function sendAccessDined() {
        while (@ob_end_clean());
        header('HTTP/1.1 403 Forbidden');
        exit();
    }
    
    protected function parseURI() {
        $request = Request::getInstance();
        $uri = trim($request->getURI(), '/');
        list($url_path,) = explode('?', $uri, 2);
        $routes = $this->routes;
        //pre($routes);
        $trim_chars = "/\n\r\0\t\x0B ";
        $url_args = array();
        while (strlen($url_path) > 1 && !(isset($routes[$url_path]))) {
            $new_arg = substr($url_path, strrpos($url_path, '/'));
            $url_path = trim(substr($url_path, 0, strrpos($url_path, $new_arg)), '/');
//             pre($url_path);
            // put the new arg into the list
            array_unshift($url_args, trim($new_arg, $trim_chars));
        }
        if ($url_path) {
            $this->actionName = $routes[$url_path];
        } else {
//             throw new \ORC\Exception\NotExistsException('404 not found');
        }
        $request->set(Request::args, $url_args);
    }
    
    protected function _parseName($name) {
        return explode('.', strtolower($name), 2);
    }
}