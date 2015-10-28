#!/usr/bin/php
<?php
use ORC\Application;
if ($argc == 0) {
	return display_help();
}
foreach ($argv as $arg) {
	if (false !== strpos($arg, '=')) {
		list($cmd, $value) = explode('=', $arg, 2);
	} else {
		$cmd = $arg;
	}
	switch ($cmd) {
		case '-h':
		case '--help':
			return display_help();
			break;
		case '-b':
		case '--app-dir':
			$dir_app_root = realpath($value);
			break;
		case '-m':
		case '--module':
			$m_name = $value;
			break;
		case '-n':
		case '--name':
			$module_name = $value;
			break;
	}
}
if (empty($m_name)) {
	return display_help();
}
if (empty($module_name)) {
	$module_name = $m_name;
}
$m_name = strtolower($m_name);
if (empty($dir_app_root)) {
	$dir_app_root = getcwd();
}
define('DIR_APP_ROOT', $dir_app_root);
require dirname(dirname(__FILE__)) . '/application.php';
Application::getApp()->setName('Tools');
require dirname(__FILE__) . '/src/createmodule.php';
$c = new CreateModule($m_name);
$c->setModuleDisplayName($module_name);
$c->confirm();
$c->run();
function display_help() {
echo <<<'EOD'
其中-m为必选
    -h, --help                  显示这个帮助信息
    -m, --module=MODULE         创建以MODULE为机器名的模块，必选
    -n, --name=MODULE_NAME      模块以MODULE_NAME为名，如果不指定则使用MODULE
    -b, --app-dir=APP_ROOT_DIR  网站的根目录，注意不是public_html目录。如果不指定使用当前目录

EOD;
	return true;
}