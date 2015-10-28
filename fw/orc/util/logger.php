<?php
namespace ORC\Util;
use Monolog\Handler\StreamHandler;
use ORC\Exception\SystemException;
class Logger {
	const defaultLoggerName = 'default';
	protected static $loggers = array();
	protected $_log_name;
	protected $_logger;
	protected $_level;
	protected $_handlers = array();
	protected $_processers = array();
	
	/**
	 * 
	 * @param string $name
	 * @return \ORC\Util\Logger
	 */
	public static function getInstance($name = null) {
		if (empty($name)) {
		    $name = self::defaultLoggerName;
		}
		//pre(debug_backtrace());
		if (!isset(self::$loggers[$name])) {
			self::$loggers[$name] = new static($name);
		}
		return self::$loggers[$name];
	}
		
	protected function __construct($name) {
		$this->_log_name = $name;
		$config = \ORC\Core\Config::getInstance();
		$servers = $config->get('log.servers');
		$server = $config->get('log.' . $name);
		if (!$server) {
		    $server = $config->get('log.' . self::defaultLoggerName);
		}
		if (!$server) {
		    throw new SystemException('Missing config for default logger', $name);
		}
		$server_info = $servers[$server['server']];
		foreach ($server as $k => $v) {
		    if ($k == 'server') continue;
		    $server_info[$k] = $v;
		}
		//loglevel
		if ($server_info['level']) {
		    $this->_level = $server_info['level'];
		} else {
		    $this->_level = self::WARNING;
		}
	    //handlers
	    if (isset($server_info['handlers'])) {
	        $handlers = $server_info['handlers'];
	        foreach ($handlers as $handler) {
	            $this->_handlers[] = $this->explainHandler($handler);
	        }
	        if (count($this->_handlers) == 0) {
	            $this->_handlers[] = new StreamHandler('php://stderr', $this->getLogLevel());
	        }
	    }
	    //processer
	    //@todo
		$this->_logger = new \Monolog\Logger($name, $this->getLogHandlers(), $this->getLogProcessers());
	}
	
	public function getLogger() {
	    return $this->_logger;
	}
	public function addDebug($message, array $context = array()) {
		return $this->_logger->addDebug($message, $context);
	}
	public function addInfo($message, array $context = array()) {
		return $this->_logger->addInfo($message, $context);
	}
	public function addNotice($message, array $context = array()) {
		return $this->_logger->addNotice($message, $context);
	}
	public function addWarning($message, array $context = array()) {
		return $this->_logger->addWarning($message, $context);
	}
	public function addError($message, array $context = array()) {
		return $this->_logger->addError($message, $context);
	}
	public function addCritical($message, array $context = array()) {
		return $this->_logger->addCritical($message, $context);
	}
	public function addEmergency($message, array $context = array()) {
		return $this->_logger->addEmergency($message, $context);
	}
	public function addAlert($message, array $context = array()) {
		return $this->_logger->addAlert($message, $context);
	}
	public function addRecord($level, $message, array $context = array()) {
		return $this->_logger->addRecord($level, $message, $context);
	}
	
	public function getLogLevel() {
		return $this->_level;
	}
	
	public function getLogHandlers() {
	    return $this->_handlers;
	}
	public function getLogProcessers() {
		return $this->_processers;
	}
	
	/**
	 * @todo add more log type support
	 * @param string $engine
	 * @throws \ORC\Exception\SystemException
	 * @return \Monolog\Handler\HandlerInterface
	 */
	protected function explainHandler($engine) {
		switch ($engine) {
			case 'file':
				$handler = new StreamHandler(DIR_APP_ROOT . DIRECTORY_SEPARATOR . 'logs'. DIRECTORY_SEPARATOR . strtolower($this->_log_name) . '.log', $this->getLogLevel());
				$handler->setFormatter(new \Monolog\Formatter\LineFormatter(null, null, false, true));
				return $handler;
				break;
			default:
				throw new \ORC\Exception\SystemException('Unknown handler');
		}
	}
	
	const DEBUG = \Monolog\Logger::DEBUG;
	const INFO = \Monolog\Logger::INFO;
	const NOTICE = \Monolog\Logger::NOTICE;
	const WARNING = \Monolog\Logger::WARNING;
	const ERROR = \Monolog\Logger::ERROR;
	const CRITICAL = \Monolog\Logger::CRITICAL;
	const ALERT = \Monolog\Logger::ALERT;
	const EMERGENCY = \Monolog\Logger::EMERGENCY;
}