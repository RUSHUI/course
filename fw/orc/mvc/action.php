<?php
namespace ORC\MVC;
abstract class Action extends ActionBlockBase {

	private $_template_name;
	private $_blocks_hidden = array();
	private $_blocks_ignore = array();
	private $_extra_block_items = array();
	/**
	 * @return \ORC\MVC\Response
	 */
	abstract public function execute();
	
	public function getName() {
	    $class_name = strtolower(get_class($this));
	    $class_name = preg_replace('/(_action)$/', '', $class_name);
	    $action_name = str_replace('_', '.', $class_name);
	    return $action_name;
	}
	
	/**
	 * 
	 * @return string|NULL
	 */
	public function getTemplateName() {
	    return $this->_template_name;
	}
	
	/**
	 * @todo maybe should be these three to View, not in Action
	 * @return array
	 */
	public function getHiddenBlocks() {
	    return $this->_blocks_hidden;
	}
	
	public function getIgnoreBlocks() {
	    return $this->_blocks_ignore;
	}
	
	public function getExtraBlockItems() {
	    return $this->_extra_block_items;
	}
	
	/**
	 * 
	 * @param string $viewName
	 * @param int $viewType
	 * @return \ORC\MVC\Response
	 */
	protected function renderView($viewName, $viewType = self::VIEW_HTML) {
	    $response = $this->_controller->getResponse();
	    $response->setViewType($viewType);
	    $response->setViewName($viewName);
	    return $response;
	}
	
	protected function HTMLView($viewName) {
	    return $this->renderView($viewName, self::VIEW_HTML);
	}
	
	protected function RAWView($viewName) {
	    return $this->renderView($viewName, self::VIEW_RAW);
	}
	
	/**
	 * set a header redirect
	 * @todo add code support, e.g. 301 302
	 * @param string $url
	 * @return \ORC\MVC\Response
	 */
	protected function redirect($url) {
	    $response = $this->_controller->getResponse();
	    $response->setViewType(self::VIEW_REDIRECT);
	    $request = $this->_controller->getRequest();
	    $refer = $request->getReferURL();
	    if ($refer) {
	        if (strpos($url, '?')) {
	            $url .= '&';
	        } else {
	            $url .= '?';
	        }
	        $url .= sprintf('%s=%s', Request::refer, urlencode($refer));
	    }
	    $response->setRedirect($url);
	    return $response;
	}
	
	/**
	 * similiar to redirect, just change the params
	 * @param string $action_name
	 * @param array $params
	 * @return \ORC\MVC\Response
	 */
	protected function RedirectAction($action_name, array $params = array()) {
	    return $this->redirect($this->_controller->generateURL($action_name, $params));
	}
	
	/**
	 * use the HTML redirect, so the page will stay in a redirect page for several seconds
	 * @param string $url
	 * @param string $title
	 * @param string $message
	 * @param enum $type const fom \ORC\APP\Response\Redirect
	 * @return \ORC\MVC\Response
	 */
	protected function HTMLRedirect($url, $title, $message, $type = \ORC\APP\Response\Redirect::TYPE_SUCCESS) {
	    $redirect = new \ORC\APP\Response\Redirect($url, $title, $message, $type);
	    $response = $this->_controller->getResponse();
	    $response->setViewType(self::VIEW_HTML);
	    $response->setViewName('Default.Redirect');
	    $response->HTMLRedirect($redirect);
	    return $response;
	}
	
	protected function JSONReturn(array $data) {
	    $response = $this->_controller->getResponse();
	    $response->setViewType(self::VIEW_JSON);
	    $response->setViewName('Default.RawOutput');
	    $response->setContent(json_encode($data));
	    return $response;
	}
	
	/**
	 * display the raw data
	 * @param string $data
	 * @return \ORC\MVC\Response
	 */
	protected function RAWReturn($data) {
	    $response = $this->_controller->getResponse();
	    $response->setViewType(self::VIEW_RAW);
	    $response->setViewName('Default.RawOutput');
	    $response->setContent($data);
	    return $response;
	}
	
	/**
	 * @return \ORC\MVC\ViewModel
	 */
	protected function getViewModel() {
	    return ViewModel::getInstance();
	}
	
	/**
	 * check whether current user have the permission
	 * @param string $permission
	 * @param array $view_info the fist parameter is the viewType, second is viewName
	 * @param \ORC\APP\User\IUser $user optional, if not set, use current user
	 * @return boolean|\ORC\MVC\Response
	 */
	protected function checkPemission($permission, array $view_info = array(self::VIEW_HTML, 'Default.403'), \ORC\APP\User\IUser $user = null) {
	    $viewType = $view_info[0];
	    $viewName = $view_info[1];
	    if (!$user) {
	        $user = $this->getMe();
	    }
	    if ($user->canDo($permission)) {
	        return true;
	    } else {
	        return $this->renderView($viewName, $viewType);
	    }
	}
	
	/**
	 * want to execute the block code, but do not want to show in the current template
	 * @param string $block_name
	 */
	public function hideBlock($block_name) {
	    $block_name = strtolower($block_name);
	    if (!in_array($block_name, $this->_blocks_hidden)) {
	       $this->_blocks_hidden[] = $block_name;
	    }
	}
	
	/**
	 * totally ignore the block, will neither execute code nor show it 
	 * @param string $block_name
	 */
	public function ignoreBlock($block_name) {
	    $block_name = strtolower($block_name);
	    if (!in_array($block_name, $this->_blocks_ignore)) {
	        $this->_blocks_ignore[] = $block_name;
	    }
	}
	
	/**
	 * this function can add some speical item to certain block
	 * The item will be add to the end of the block
	 * @param string $block_name
	 * @param string $name
	 */
	public function addToBlock($block_name, $name) {
	     if (!isset($this->_extra_block_items[$block_name])) {
	         $this->_extra_block_items[$block_name] = array();
	     }
	     $this->_extra_block_items[$block_name][] = $name;
	}
	
	
	/**
	 * This method allowed the action to overwrite the action want to use defined in template yml file
	 * @param string $template_name
	 */
	public function useTemplate($template_name) {
	    $this->_template_name = $template_name;
	}
	
	protected function generateURL($action_name = null, array $params = array()) {
	    return $this->getController()->generateURL($action_name, $params);
	}
	

	const VIEW_HTML = 1;
	const VIEW_JSON = 2;
	const VIEW_XML = 3;
	const VIEW_RAW = 50;
	const VIEW_NOTEXISTS = 0;
	const VIEW_REDIRECT = 100;
}