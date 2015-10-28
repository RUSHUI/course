<?php
namespace ORC\API\Beanstalkd\Connection;
use ORC\API\Beanstalkd\Job;
interface IConnection {
    const DEFAULT_PORT = 11300;
    const DEFAULT_DELAY = 0; // no delay
    const DEFAULT_PRIORITY = 1024; // most urgent: 0, least urgent: 4294967295
    const DEFAULT_TTR = 60; // 1 minute
    const DEFAULT_TUBE = 'default';
    
    public function getClientName();
    /**
     * 
     * @param string $tube
     * @return bool
     */
    public function useTube($tube);
    /**
     * 
     * @param string $data
     * @param int $priority
     * @param int $delay
     * @param int $ttr
     * @return int/bool job id, false if failed
     */
    public function put($data, $priority = self::DEFAULT_PRIORITY, $delay = self::DEFAULT_DELAY, $ttr = self::DEFAULT_TTR);
    
    /**
     * bury a job
     * @param \ORC\API\Beanstalkd\Job $job
     * @param int $priority
     * @return boolean
     */
    public function bury(Job $job, $priority = self::DEFAULT_PRIORITY);
    /**
     * delete a job
     * @param \ORC\API\Beanstalkd\Job $job
     * @return boolean
     */
    public function delete(Job $job);
    /**
     * 
     * @param \ORC\API\Beanstalkd\Job $job
     */
    public function kickJob(Job $job);
    /**
     * 
     * @param int $max
     */
    public function kick($max);
    
    /**
     * 
     * @param \ORC\API\Beanstalkd\Job $job
     * @param int $priority
     * @param int $delay
     */
    public function release(Job $job, $priority = self::DEFAULT_PRIORITY, $delay = self::DEFAULT_DELAY);
    /**
     * 
     * @param int $timeout
     * @return \ORC\API\Beanstalkd\Job job
     */
    public function reserve($timeout = null);
    /**
     * 
     * @param \ORC\API\Beanstalkd\Job $job
     */
    public function touch(Job $job);
    /**
     * @return array
     */
    public function listTubes();
    /**
     * 
     * @param bool $askServer whether to get the tubes from server
     */
    public function listTubesWatched($askServer = false);
    /**
     * 
     * @param bool $askServer
     */
    public function listTubeUsed($askServer = false);
    /**
     * Remove the specified tube from the watchlist
     * @param string $tube
     */
    public function ignore($tube);
    /**
     * Add the specified tube to the watchlist, to reserve jobs from.
     * @param string $tube
     */
    public function watch($tube);
}