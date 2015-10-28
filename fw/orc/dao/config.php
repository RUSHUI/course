<?php
namespace ORC\DAO;
use ORC\Exception\SystemException;
class Config {
    const DEFAULT_NAME = 'default';
    private static $instance;
    
    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private $_all_servers = array();
    private $_servers = array();
    private function __construct() {
        $config = \ORC\Core\Config::getInstance();
        $this->_all_servers = $config->get('database.servers');
        $default_server = $config->get('database.' . self::DEFAULT_NAME);
        foreach ($config->get('database') as $server => $server_cfg) {
            if ($server == 'servers') continue;
            $server_name = $server_cfg['server'];
            if (isset($this->_all_servers[$server_name])) {
                $server_info = $this->_all_servers[$server_name];
            } else {
                if (isset($default_server) && isset($this->_all_servers[$default_server['server']])) {
                    $server_info = $this->_all_servers[$default_server['server']];
                } else {
                    throw new SystemException('Default database is not set', $server);
                }
            }
            foreach ($server_cfg as $k => $v) {
                if ($k == 'server') continue;
                $server_info[$k] = $v;
            }
            //parse the dsn
            if (!isset($server_info['dsn'])) {
                $server_info['dsn'] = $this->getDSN($server_info);
            }
            $this->_servers[$server] = $server_info;
        }
    }
    
    public function getServerInfo($name = null) {
        if (empty($name)) {
            $name = self::DEFAULT_NAME;
        }
        return isset($this->_servers[$name]) ? $this->_servers[$name] : false;
    }
    
    public function getServers() {
        return $this->_servers;
    }
    
    private function getDSN(array $server_info) {
        switch ($server_info['engine']) {
            case 'mysql':
                $options = array();
                $avaliables = array('host', 'port', 'dbname', 'unix_socket', 'charset');
                foreach ($server_info as $option => $value) {
                    if (in_array($option, $avaliables)) {
                        $options[] = sprintf('%s=%s', $option, $value);
                    }
                }
                return sprintf('%s:%s', $server_info['engine'], implode(';', $options));
                break;
            default:
                throw new SystemException('unknown db engine', $server_info);
        }
    }
}