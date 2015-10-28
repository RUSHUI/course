<?php
namespace ORC\API\Beanstalkd;
use ORC\Core\Config;
use ORC\Exception\ConfigException;
use ORC\Exception\SystemException;
use ORC\API\Beanstalkd\Connection\IConnection;
class Connection {
    const DEFAULT_CLIENT = 'pheanstalk';
    protected static $connections = array();
    /**
     * 
     * @param unknown $tube
     * @return \ORC\API\Beanstalkd\Connection\IConnection
     */
    public static function get($tube, $createNew = false) {
        $config = Config::getInstance();
        if ($config->exists('beanstalkd.client')) {
            $client = $config->get('beanstalkd.client');
        } else {
            $client = self::DEFAULT_CLIENT;
        }
        $server = self::getServer($tube);
        $hash = sprintf('%s://%s:%d', $client, $server['ip'], $server['port']);
        if (!isset(self::$connections[$hash])) {
            self::$connections[$hash] = array();
        }
        if (count(self::$connections[$hash]) == 0 || $createNew) {
            $connection = self::createConnection($client, $server);
            self::$connections[$hash][] = $connection;
            return $connection;
        }
        foreach (self::$connections as $connection) {
            return $connection;
        }
    }
    
    public static function getAllConnections() {
        return self::$connections;
    }
    
    protected static function getServer($tube) {
        $config = Config::getInstance();
        $servers = $config->get('beanstalkd.servers');
        $routers = $config->get('beanstalkd.routers');
        if (!isset($routers[$tube])) {
            //             return new ConfigException('Unknow Router Name', $name);
            $server_name = self::DEFAULT_SERVER;
        } else {
            $server_name = $routers[$tube];
        }
        if (isset($servers[$server_name])) {
            $server = $servers[$server_name];
        } else {
            $server = $servers[self::DEFAULT_SERVER];
        }
        if (empty($server)) {
            throw new ConfigException('UNKNOW Beanstalkd Server Name');
        }
    
        if (!isset($server['port'])) {
            $server['port'] = self::DEFAULT_PORT;
        }
        return $server;
    }
    
    /**
     * 
     * @param unknown $client
     * @param unknown $server
     * @throws SystemException
     * @return \ORC\API\Beanstalkd\Connection\IConnection
     */
    protected static function createConnection($client, $server) {
        $class_name = '\ORC\API\Beanstalkd\Connection\\' . $client;
        if (class_exists($class_name)) {
            $connection = new $class_name($server);
            if ($connection instanceof IConnection) {
                return $connection;
            }
        }
        throw new SystemException('Unknow client!');
        
    }
}