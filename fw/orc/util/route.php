<?php
namespace ORC\Util;
use ORC\Core\Config;
class Route extends \ORC\Util\Container {

    const TABLENAME_ROUTES = 'orc__routes';
	protected static $_instance;
	protected $_request;
	protected $_route_table_inited = false;
	/**
	 *
	 * @return \ORC\Util\Route
	 */
	public static function getInstance() {
		if (!isset(self::$_instance) || !is_object(self::$_instance)) {
			self::$_instance = new static(\ORC\MVC\Request::getInstance());
		}
		return self::$_instance;
	}

	protected function __construct(\ORC\MVC\Request $request) {
		$this->_request = $request;
		$this->initRouteTable();
		$this->parseURI();
	}

	public function getActionName() {
		return $this->get('action');
	}

	/**
	 * get all actions
	 * @return Array
	 */
	public function getAllActions() {
		return $this->get('actions');
	}

	public function getAllModules() {
		return $this->get('modules');
	}

	public function getRouteTable() {
		return $this->get('routes');
	}

	protected function parseURI() {
		$uri = $this->_request->getURI();
		list($url_path,) = explode('?', $uri, 2);
		$config = Config::getInstance();
		if ($config->get('app.seo.ext')) {
		    $url_path = preg_replace('/\.(' . $config->get('app.seo.ext') . ')$/i', '', $url_path);
		}
		$routes = $this->get('routes');
		//pre($routes);
		$trim_chars = "/\n\r\0\t\x0B ";
		$url_args = array();
		while (strlen($url_path) > 1 && !(isset($routes[$url_path]))) {
			$new_arg = substr($url_path, strrpos($url_path, '/'));
			$url_path = substr($url_path, 0, strrpos($url_path, $new_arg));

			// put the new arg into the list
			array_unshift($url_args, trim($new_arg, $trim_chars));
		}
		if ($url_path == '') {
		    $url_path = '/';
		}
		if ($url_path == '/') {
			//index, show the default action
			$this->set('action', strtolower(\ORC\Application::getApp()->getDefaultAction()));
		} elseif ($url_path) {
			$this->set('action', strtolower($routes[$url_path]));
		} else {
			throw new \ORC\Exception\NotExistsException('404 not found');
		}
		$this->_request->set(\ORC\MVC\Request::args, $url_args);
	}

	protected function initRouteTable() {
		if ($this->_route_table_inited) return;
		//@todo read from cache
		$data = null;
		if (empty($data)) {
			$modules = array();
			$actions = array();
			$this->_findModules('', DIR_APP_MODULE_ROOT, $modules);
			foreach ($modules as $m_name => $module_info) {
				$this->_findActions($module_info, $actions);
			}
			$routes = array();
			foreach ($actions as $path => $action) {
				$action_name = $action['module']['m_name'] . '.' . $action['name'];
				$routes[$path] = strtolower($action_name);
			}
			$data = array('modules' => $modules, 'actions' => $actions, 'routes' => $routes);
		} else {
			$modules = $data['modules'];
			$actions = $data['actions'];
			$routes = $data['routes'];
		}
		$this->set('modules', $modules);
		$this->set('actions', $actions);
		$this->set('routes', $routes);
		$this->_route_table_inited = true;
	}

	protected function _findModules($path, $base, &$modules) {
		$d = dir($base . $path);

		while (false !== ($entry = $d->read())) {
			if ($entry == '.' || $entry == '..') continue;
			$find_path = $base . $path . DIRECTORY_SEPARATOR . $entry;
			if (is_dir($find_path)) {
				$module_info = $this->_checkModulePath($find_path . DIRECTORY_SEPARATOR, $entry);
				if ($module_info) {
					if (isset($modules[$module_info['m_name']])) {
						throw new \ORC\Exception\SystemException('Duplicate module ' . $module_info['m_name'] . ' found!');
					}
					$module_info['path'] = ltrim($path . DIRECTORY_SEPARATOR . $entry, DIRECTORY_SEPARATOR);
					$module_info['filepath'] = $find_path;
					$modules[$module_info['m_name']] = $module_info;
				}
				$this->_findModules($path . DIRECTORY_SEPARATOR . $entry, $base, $modules);
			}
		}
		$d->close();
	}

	protected function _checkModulePath($path, $module_path) {
		/**
		 * @todo check whether a folder contains a module
		 * should do more about the content, etc
		 */
		//pre("check $path");
		if (file_exists($path . 'module.info')) {
			$module_info = array();
			$module_info['name'] = $module_path;
			$module_info['m_name'] = $module_path;
			return $module_info;
		}
		return false;
	}

	protected function _findActions(Array $module_info, &$actions) {
		$path = $module_info['filepath'] . DIRECTORY_SEPARATOR . 'actions';
		if (!file_exists($path) || !is_dir($path)) {
			return false;
		}
		$this->_checkActionPath($path, $module_info, $actions);
	}

	protected function _checkActionPath($path, $module_info, &$actions) {
		$d = dir($path);
		while (false !== ($entry = $d->read())) {
			if ($entry == '.' || $entry == '..') continue;
			$filename = $path . DIRECTORY_SEPARATOR . $entry;
			if (is_dir($filename)) {
				$this->_checkActionPath($filename, $module_info, $actions);
				continue;
			}
			//pre("checking $entry");
			if (preg_match('/^([a-z0-9_]+)\.action\.php$/i', $entry, $matches)) {
				//action file found

				$action_info = $this->_parseAction($filename, $matches[1], $module_info);

				if (empty($action_info)) continue;
				if (isset($actions[$action_info[0]])) {
					throw new \ORC\Exception\SystemException('Duplicate action ' . $action_info[0] . ' found!');
				}
				$action = array();
				$action['module'] = $module_info;
				$action['filepath'] = $filename;
				$action['name'] = $action_info[1];
				$action['path'] = $action_info[0];
				$actions[$action_info[0]] = $action;
			}
		}
		$d->close();
	}

	protected function _parseAction($filename, $action_file_name, $module_info) {
		//@todo try to use Annotation function @Route
		//now temporary use action name
		$content = file_get_contents($filename);
		if (preg_match('/class ' . $module_info['m_name'] . '_([a-z0-9_]+)_Action\sextends\s/i', $content, $matches)) {
			//pre($matches);
			$action_name = str_replace('_', '.', $matches[1]);
			$route = '/' . $module_info['m_name'] . '/' . strtolower(str_replace('_', '/' , $matches[1]));
			return array($route, $action_name);
		} else {

		}
		return null;
	}
}
