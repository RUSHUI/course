<?php
namespace ORC\API\Beanstalkd;
use ORC\API\Beanstalkd\Connection\IConnection;
class Consumer {
    protected $tube;
    protected $connection;
    public function __construct($tube) {
        $this->connection = Connection::get($tube, true);
        $this->tube = $tube;
        $this->connection->watch($tube);
    }
    
    public function reserve($timeout = null) {
        return $this->connection->reserve();
    }
    
    public function release($job, $priority = IConnection::DEFAULT_PRIORITY, $delay = IConnection::DEFAULT_DELAY) {
        return $this->connection->release($job, $priority, $delay);
    }
    
    public function delete($job) {
        return $this->connection->delete($job);
    }
    
    public function bury($job, $priority = IConnection::DEFAULT_PRIORITY) {
        return $this->connection->bury($job, $priority);
    }
    
    public function touch($job) {
        return $this->connection->touch($job);
    }
    
//     public function watch($tube) {
//         return $this->connection->watch($tube);
//     }
    
//     public function ignore($tube) {
//         return $this->connection->ignore($tube);
//     }
}