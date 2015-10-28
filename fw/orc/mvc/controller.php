<?php
namespace ORC\MVC;
use ORC\Application;
use ORC\Util\Route;
use ORC\Exception\SystemException;
use ORC\Exception\NotExistsException;
/**
 * only for dispatch
 * @author Zhou Yanqin
 *
 */
class Controller {
    
    protected $_models = array();
    protected $_action;
    protected $_view;
    
    protected $_response;
    
	public function __construct() {
	}
	
	public function dispatch() {
		$route = \ORC\Util\Route::getInstance();
		return $route->getActionName();
	}
	
	public function execute($action_name) {
		if ($action_name == '') {
			$action_name = Application::getApp()->getDefaultAction();
		}
		$action = $this->_retrieveAction($action_name);
		if ($action == null) {
			throw new \ORC\Exception\ActionNotFoundException('Action ' . $action_name . ' Not Found!');
		}
		while (method_exists($action, 'forward')) {
			$new_action_name = $action->forward();
			if ($new_action_name) {
				$action = $this->_retrieveAction($new_action_name);
				$action_name = $new_action_name;
			} else {
				break;
			}
		}
		$this->_action = $action;
		if (method_exists($action, 'pre_execute')) {
			$response = $action->pre_execute();
		}
		//if pre_execute returns something, the default execute will not be used
		if (!isset($response) || !$response) {
			$response = $action->execute();
		}
		if (!$response) {
			if (method_exists($action, 'post_execute')) {
				$response = $action->post_execute();
			}
		}
		//result is the Response object
		if (!$response) {
			throw new \ORC\Exception\ActionException('Action Have No Result', $action_name);
		}
		if (!($response instanceof Response)) {
		    throw new \ORC\Exception\ActionException('Wrong result from action', $action_name);
		}
		if ($response->getViewType() == Action::VIEW_REDIRECT) {
		    $response->sendHeaders();
		    $response->redirect();
		    exit();
		}
		$view_name = $response->getViewName();
		$view = $this->_retrieveView($view_name);
		if ($view == null) {
			throw new \ORC\Exception\ViewNotFoundException('View Not Found!', $view_name);
		}
		while (method_exists($view, 'forward')) {
			$new_view_name = $view->forward();
			if ($new_view_name) {
				$view = $this->_retrieveView($new_view_name);
				$view_name = $new_view_name;
			} else {
				break;
			}
		}
		$this->_view = $view;
		$output = $response->render($view);
		$response->sendHeaders();
		echo $output;
	}
	
	/**
	 * get current action
	 * @return \ORC\MVC\Action
	 */
	public function getAction() {
	    return $this->_action;
	}
	
	/**
	 * 
	 * @return \ORC\MVC\View
	 */
	public function getView() {
	    return $this->_view;
	}
	/**
	 *
	 * @param string $name
	 * @return \ORC\MVC\Model
	 */
	public function getModel($name) {
        $name = strtolower($name);
        if (isset($this->_models[$name])) {
            return $this->_models[$name];
        }
        return $this->_models[$name] = $this->_retrieveModel($name);
	}
	
	/**
	 * only used in viewmodel
	 * @return \ORC\MVC\Model[]
	 */
	public function getModels() {
	    return $this->_models;
	}
	
	public function getFilePath($type, $name, $check_exists = true) {
	    list($module_name, $extra) = $this->_parseName($name);
	    $type = strtolower($type);
	    switch($type) {
	        case 'action':
	        case 'model':
	        case 'view':
	        case 'template':
	        case 'block':
	            $filename = DIR_APP_MODULE_ROOT . DIRECTORY_SEPARATOR . $module_name . DIRECTORY_SEPARATOR . $type . 's';
	            $filename .= DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $extra) . '.' . $type . '.php';
	        break;
	        default:
	            throw new SystemException('Unknown file type');
	    }
	    if ($check_exists) {
	        if (file_exists($filename)) {
	            return $filename;
	        } else {
	            if ($module_name == 'default') {
	                $filename = DIR_ORC_ROOT . DIRECTORY_SEPARATOR . $module_name . DIRECTORY_SEPARATOR . $type . 's';
	                $filename .= DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $extra) . '.' . $type . '.php';
	                if (file_exists($filename)) {
	                    return $filename;
	                }
	            }
	            throw new NotExistsException('File not exist', $filename);
	        }
	    } else {
	        return $filename;
	    }
	}
	
	/**
	 * @return \ORC\MVC\Request
	 */
	public function getRequest() {
	    return Request::getInstance();
	}
	
	/**
	 * 
	 * @return \ORC\MVC\Response
	 */
	public function getResponse() {
	    if (!isset($this->_response)) {
	        $this->_response = new Response($this);
	    }
	    return $this->_response;
	}
	
	public function generateURL($action_name = null, array $params = array()) {
	    return \ORC\Util\Url::generateURL($action_name, $params);
	}
	/**
	 * 
	 * @param string $name
	 * @return Ambigous <\ORC\MVC\Action, unknown>|NULL
	 */
	protected function _retrieveAction($name) {
		list($module_name, $action_name) = $this->_parseName($name);
		
		$class_name = sprintf('%s_%s_Action', $module_name, str_replace('.', '_', $action_name));
		if (!class_exists($class_name)) {
			$found = false;
			$filename = DIR_APP_MODULE_ROOT . DIRECTORY_SEPARATOR . $module_name . DIRECTORY_SEPARATOR . 'actions' . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $action_name) . '.action.php';
			//var_dump($filename);exit();
			if (file_exists($filename)) {
				include_once $filename;
				$found = true;
			} else {
				//try to find from route table
				$actions = Route::getInstance()->getAllActions();
				foreach ($actions as $action) {
					if ($module_name == $action['module']['name'] && $action_name == $action['name']) {
						$filename = $action['filepath'];
						if (file_exists($filename)) {
							include_once $filename;
							$found = true;
						}
					}
				}
			}
			if (!$found) {
				if ($module_name == 'default') {
					$filename = DIR_ORC_ROOT . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'actions' . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $action_name) . '.action.php';
					if (file_exists($filename)) {
						include_once $filename;
						$found = true;
					}
				}
			}
		}
		
		if (class_exists($class_name)) {
			$class = new $class_name($this);
			if ($class instanceof \ORC\MVC\Action) {
				return $class;
			}
		}
		return null;
	}
	
	protected function _retrieveModel($name) {
	    list($module_name, $model_name) = $this->_parseName($name);
	    
	    $class_name = sprintf('%s_%s_Model', $module_name, str_replace('.', '_', $model_name));
	    if (!class_exists($class_name)) {
	        $filename = DIR_APP_MODULE_ROOT . DIRECTORY_SEPARATOR . $module_name . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $model_name) . '.model.php';
	        //pre($filename);
	        if (file_exists($filename)) {
	            include_once $filename;
	        } elseif ($module_name == 'default') {
	            $filename = DIR_ORC_ROOT . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $model_name) . '.model.php';
	            if (file_exists($filename)) {
	                include_once $filename;
	            }
	        }
	    }
	    
	    if (class_exists($class_name)) {
	        $class = new $class_name($this);
	        if ($class instanceof \ORC\MVC\Model) {
	            return $class;
	        }
	    }
	    throw new \ORC\Exception\NotExistsException('Model not found');
	}
	
	/**
	 * 
	 * @param string $name
	 * @return Ambigous <\ORC\MVC\View, unknown>|NULL
	 */
	protected function _retrieveView($name) {
		list($module_name, $view_name) = $this->_parseName($name);
		
 		$class_name = sprintf('%s_%s_View', $module_name, str_replace('.', '_', $view_name));
		if (!class_exists($class_name)) {
			$filename = DIR_APP_MODULE_ROOT . DIRECTORY_SEPARATOR . $module_name . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $view_name) . '.view.php';
			if (file_exists($filename)) {
				include_once $filename;
			} elseif ($module_name == 'default') {
				$filename = DIR_ORC_ROOT . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $view_name) . '.view.php';
				if (file_exists($filename)) {
					include_once $filename;
				}
			}
		}
		
		if (class_exists($class_name)) {
			$class = new $class_name($this);
			if ($class instanceof \ORC\MVC\View) {
				return $class;
			}
		}
		return null;
	}
	
	protected function _parseName($name) {
		return explode('.', strtolower($name), 2);
	}
}