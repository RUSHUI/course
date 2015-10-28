<?php
namespace ORC {
use ORC\Core\Config;
require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
spl_autoload_register(array('\ORC\Loader', 'autoload'));
class Application {
	protected static $_app;
	public static function getApp() {
		if (!isset(self::$_app)) {
			self::$_app = new static();
		}
		return self::$_app;
	}
	
	protected $_default_action;
	protected $_controller;
	protected $_name;
	
	protected function __construct() {
		if (!defined('DIR_APP_ROOT')) {
			throw new \ORC\Exception\SystemException('const DIR_APP_ROOT Not Defined!');
		}
		if (!defined('DIR_ORC_ROOT')) {
			define('DIR_ORC_ROOT', dirname(__FILE__));
		}
		if (!defined('DIR_APP_MODULE_ROOT')) {
			define('DIR_APP_MODULE_ROOT', DIR_APP_ROOT . DIRECTORY_SEPARATOR . 'modules');
		}
		if (!defined('DIR_APP_CONFIG_ROOT')) {
			define('DIR_APP_CONFIG_ROOT', DIR_APP_ROOT . DIRECTORY_SEPARATOR . 'config');
		}
		if (!defined('DIR_APP_TEMPLATE_ROOT')) {
		    define('DIR_APP_TEMPLATE_ROOT', DIR_APP_ROOT . DIRECTORY_SEPARATOR . 'templates');
		}
		if (!defined('DIR_APP_VENDOR_ROOT')) {
		    define('DIR_APP_VENDOR_ROOT', DIR_APP_ROOT . DIRECTORY_SEPARATOR . 'vendor');
		}
		$vender_autoload_file = DIR_APP_VENDOR_ROOT . DIRECTORY_SEPARATOR . 'autoload.php';
		if (file_exists($vender_autoload_file)) {
		    require $vender_autoload_file;
		}
		//load config
		Config::getInstance();
	}
	
	public function run($action_name = null) {
		//load templates
		\ORC\Util\TemplateManager::getInstance();
		if (!defined('DIR_APP_PUBLIC')) {
			throw new \ORC\Exception\SystemException('const DIR_APP_PUBLIC not defined!');
		}
		if (empty($action_name)) {
			$action_name = $this->getController()->dispatch();
		}
		try {
		    $this->getController()->execute($action_name);
		} catch (\ORC\Exception\Exception $ex) {
		    $this->getController()->getRequest()->set('exception', $ex);
		    $this->getController()->execute('Default.Exception');
		} catch (\Exception $ex) {
		    $this->getController()->getRequest()->set('exception', $ex);
		    $this->getController()->execute('Default.Exception');
		}
	}
	
	public function setDefaultAction($action) {
		$this->_default_action = $action;
		return $this;
	}
	
	public function getDefaultAction() {
		if (!isset($this->_default_action)) {
			$default = Config::getInstance()->get('app.default');
			if (empty($default)) {
				$this->_default_action = 'Default.Index';
			} else {
				$this->_default_action = $default;
			}
		}
		return $this->_default_action;
	}

	public function setName($name) {
		$this->_name = $name;
		return $this;
	}
	
	public function getName() {
		return $this->_name;
	}
	
	public function setDefaultController(\ORC\MVC\Controller $controller) {
		$this->_controller = $controller;
		return $this;
	}
	
	public function getController() {
		if (!isset($this->_controller)) {
			$this->_controller = new \ORC\MVC\Controller();
		}
		return $this->_controller;
	}
}

class Loader {
	public static function autoload($class_name) {
		list($namespace, $name) = self::parseClassName($class_name);
		switch (strtolower($namespace)) {
			default:
				if (strcasecmp(substr($namespace, 0, 4), "orc\\") == 0) {
					$filename = DIR_ORC_ROOT . DIRECTORY_SEPARATOR . str_replace("\\", DIRECTORY_SEPARATOR, strtolower($class_name)) . '.php';
					if (file_exists($filename)) {
						return include $filename;
					} else {
						//throw new \ORC\Exception\ClassNotFoundException($class_name);
					}
				} elseif (strcasecmp(substr($namespace, 0, 11), "app\\module\\") == 0) {
				    if ($filename = self::tryAppModuleClass($namespace, $name)) {
				        return include $filename;
				    } else {
				        //throw new \ORC\Exception\ClassNotFoundException($class_name);
				    }
				} elseif (strcasecmp(substr($namespace, 0, 11), "app\\cronjob") == 0) {
				    if ($filename = self::tryAppCronClass($namespace, $name)) {
				        return include $filename;
				    } else {
				        //throw new \ORC\Exception\ClassNotFoundException($class_name);
				    }
				}
				break;
		}
	}
	
	public static function parseClassName($class_name) {
		$pos = strrpos($class_name, "\\");
		if (false !== $pos) {
			$namespace = substr($class_name, 0, $pos);
			$name = substr($class_name, $pos+1);
		} else {
			$namespace = "\\";
			$name = $class_name;
		}
		return array($namespace, $name);
	}
	
	protected static function tryAppModuleClass($namespace, $class_name) {
	    $namespaces = explode("\\", substr(strtolower($namespace), 11));
	    $module_name = $namespaces[0];
	    $filename = DIR_APP_MODULE_ROOT . DIRECTORY_SEPARATOR . $module_name . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR;
	    array_shift($namespaces);
	    if (count($namespaces)) {
	       $filename .= implode(DIRECTORY_SEPARATOR, $namespaces) . DIRECTORY_SEPARATOR;
	    }
	    $filename .= strtolower($class_name) . '.php';
// 	    if (!file_exists($filename)) {
// 	        //@todo try another place
// 	    }
	    if (file_exists($filename)) {
	        return $filename;
	    }
	    return false;
	}
	
	protected static function tryAppCronClass($namespace, $class_name) {
	    $class_folder = strtolower(substr($namespace, 11));
	    if ($class_folder) {
	        $class_folder = str_replace("\\", DIRECTORY_SEPARATOR, $class_folder);
	    }
	    $filename = DIR_APP_ROOT . DIRECTORY_SEPARATOR . 'cronjobs' . DIRECTORY_SEPARATOR . 'classes';
	    $filename .= $class_folder . DIRECTORY_SEPARATOR;
	    $filename .= strtolower($class_name) . '.php';

// 	    if (!file_exists($filename)) {
// 	        //@todo try another place
// 	    }
	    if (file_exists($filename)) {
	        return $filename;
	    }
	    return false;
	}
}
}
namespace {
function pre() {
    $obj = \ORC\Util\Pre::getInstance ();
    $obj->output ( func_get_args () );
}
}