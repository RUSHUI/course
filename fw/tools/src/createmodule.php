<?php
class CreateModule {
	private $_module_name;
	private $_module_info = array();
	public function __construct($m_name) {
		$this->_module_name = $m_name;
	}
	
	public function setModuleDisplayName($name) {
		$this->_module_info['name'] = $name;
	}
	
	public function confirm() {
		echo "要在" . DIR_APP_ROOT . '/modules/' . $this->_module_name . '创建一个模块，模块名称为' . $this->_module_info['name'] . "。\n";
		$line = readline("确认请输入Y/y，否则输入其他任意键:");
		if ($line != 'y' && $line != 'Y') {
			echo "取消\n";
			exit();
		}
	}
	
	public function run() {
		echo "开始创建模块\n";
	}
}