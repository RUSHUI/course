<?php
namespace ORC\API\Beanstalkd;
use ORC\Exception\SystemException;
class Job {
    protected $client;
    protected $job;
    protected $cmd;
    protected $params = array();
    public function __construct($client, $job) {
        $this->client = $client;
        switch ($client) {
            case 'pheanstalk':
                if (!($job instanceof \Pheanstalk\Job)) {
                    throw new SystemException('Job not correct', $job);
                }
                $this->job = $job;
                $data = $job->getData();
                $data = json_decode($data, true);
                $this->cmd = $data[0];
                $this->params = is_array($data[1]) ? $data[1] : array();
                break;
            default:
                throw new SystemException('Unknow Client');
        }
    }
    
    /**
     * @return string
     */
    public function getClient() {
        return $this->client;
    }
    
    /**
     * @return mixed;
     */
    public function getJob() {
        return $this->job;
    }
    
    public function getCmd() {
        return $this->cmd;
    }
    
    public function getParams() {
        return $this->params;
    }
}