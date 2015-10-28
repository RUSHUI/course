#!/usr/bin/php
<?php
use ORC\Application;
if ($argc == 0) {
	return display_help();
}
$ignore_exists = false;
$yes_to_all = false;
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
		case '-i':
		case '--ignore-exists':
			$ignore_exists = true;
			break;
		case '-y':
		    $yes_to_all = true;
		    break;
	}
}
if (empty($dir_app_root)) {
	$dir_app_root = getcwd();
}
define('DIR_APP_ROOT', $dir_app_root);
require dirname(dirname(__FILE__)) . '/application.php';
Application::getApp()->setName('Tools');
require dirname(__FILE__) . '/src/schema.php';
$schema = new Schema($ignore_exists, $yes_to_all);
$schema->createSchema();
function display_help() {
    echo <<<'EOD'
    -h, --help                  显示这个帮助信息
    -i, --ignore-exists         加上这个参数会强制生成所有表而不忽略已经存在的
    -b, --app-dir=APP_ROOT_DIR  网站的根目录，注意不是public_html目录。如果不指定使用当前目录
    -y                          自动对所有的问题使用Yes
EOD;
    return true;
}