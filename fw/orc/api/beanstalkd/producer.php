<?php
namespace ORC\API\Beanstalkd;
class Producer {
    protected $connection;
    protected $tube;
    public function __construct($tube, $newConnection = false) {
        $this->connection = Connection::get($tube, $newConnection);
        $this->tube = $tube;
    }
    
    /**
     * 
     * @param string $cmd 任务名称
     * @param array $params 参数
     * @return int job id
     */
    public function put($cmd, array $params = array()) {
        $data = array($cmd, $params);
        $this->connection->useTube($this->tube);
        return $this->connection->put(json_encode($data));
    }
}