<?php
// $start = microtime(true);
$base = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR;
define('DIR_ORC_ROOT',  $base. 'fw');//the root path of the framework
define('DIR_APP_ROOT', dirname(dirname(__FILE__)));//the root path of this site(application)
define('DIR_APP_PUBLIC', dirname(__FILE__));//the path of the public html folder
//define('APP_IN_DEBUG_MODE', true);
require DIR_ORC_ROOT . DIRECTORY_SEPARATOR . 'application.php';
\ORC\Application::getApp()->setName('example')->setDefaultAction('Index.Index')->run();
//var_dump(microtime(true) - $start);
