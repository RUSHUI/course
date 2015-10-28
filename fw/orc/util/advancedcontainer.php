<?php
namespace ORC\Util;
/**
 * The advanced container
 */
class AdvancedContainer extends Container {
	/**
	 * get a value
	 * @param mixed $k
	 * @return mixed
	 */
	public function get($k) {
		$p = str_replace('.', "']['", $k);
		return @eval("return \$this->_data['$p'];");
	}
	
	/**
	 * set a value
	 * @param string $k
	 * @param mixed $v
	 * @return mixed the value
	 */
	public function set($k, $v) {
		$p = str_replace('.', "']['", $k);
		eval ("\$this->_data['$p'] = \$v;");
		return $v;
	}
	
	public function exists($k) {
		$p = str_replace('.', "']['", $k);
		eval("\$v = isset(\$this->_data['$p']);");
		return $v;
	}
	
	public function remove($k) {
		$p = str_replace('.', "'[]'", $k);
		eval("unset(\$this->_data['$p']);");
	}
}