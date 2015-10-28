<?php
namespace ORC\API\Beanstalkd\Connection;
use ORC\API\Beanstalkd\Job;
class Pheanstalk implements IConnection {
    protected $pheanstalk;
    protected $server;
    
    /* (non-PHPdoc)
     * @see \ORC\API\Beanstalkd\Connection\IConnection::getClientName()
     */
    public function getClientName()
    {
        return 'pheanstalk';
    }

    public function __construct($server) {
        $this->pheanstalk = new \Pheanstalk\Pheanstalk($server['ip'], $server['port']);
    }
    
 /* (non-PHPdoc)
     * @see \ORC\API\Beanstalkd\Connection\IConnection::bury()
     */
    public function bury(Job $job, $priority = self::DEFAULT_PRIORITY)
    {
        return $this->pheanstalk->bury($job->getJob(), $priority);
    }

 /* (non-PHPdoc)
     * @see \ORC\API\Beanstalkd\Connection\IConnection::delete()
     */
    public function delete(Job $job)
    {
        return $this->pheanstalk->delete($job->getJob());
    }

 /* (non-PHPdoc)
     * @see \ORC\API\Beanstalkd\Connection\IConnection::ignore()
     */
    public function ignore($tube)
    {
        return $this->pheanstalk->ignore($tube);
    }

 /* (non-PHPdoc)
     * @see \ORC\API\Beanstalkd\Connection\IConnection::kick()
     */
    public function kick($max)
    {
        return $this->pheanstalk->kick($max);
    }

 /* (non-PHPdoc)
     * @see \ORC\API\Beanstalkd\Connection\IConnection::kickJob()
     */
    public function kickJob(Job $job)
    {
        return $this->pheanstalk->kickJob($job->getJob());
    }

 /* (non-PHPdoc)
     * @see \ORC\API\Beanstalkd\Connection\IConnection::listTubes()
     */
    public function listTubes()
    {
        return $this->pheanstalk->listTubes();
    }

 /* (non-PHPdoc)
     * @see \ORC\API\Beanstalkd\Connection\IConnection::listTubesWatched()
     */
    public function listTubesWatched($askServer = false)
    {
        return $this->pheanstalk->listTubesWatched($askServer);
    }

 /* (non-PHPdoc)
     * @see \ORC\API\Beanstalkd\Connection\IConnection::listTubeUsed()
     */
    public function listTubeUsed($askServer = false)
    {
        return $this->pheanstalk->listTubeUsed($askServer);
    }

 /* (non-PHPdoc)
     * @see \ORC\API\Beanstalkd\Connection\IConnection::put()
     */
    public function put($data, $priority = self::DEFAULT_PRIORITY, $delay = self::DEFAULT_DELAY, $ttr = self::DEFAULT_TTR)
    {
        return $this->pheanstalk->put($data, $priority, $delay, $ttr);
    }

 /* (non-PHPdoc)
     * @see \ORC\API\Beanstalkd\Connection\IConnection::release()
     */
    public function release(Job $job, $priority = self::DEFAULT_PRIORITY, $delay = self::DEFAULT_DELAY)
    {
        return $this->pheanstalk->release($job->getJob(), $priority, $delay);
    }

 /* (non-PHPdoc)
     * @see \ORC\API\Beanstalkd\Connection\IConnection::reserve()
     */
    public function reserve($timeout = null)
    {
        $job = $this->pheanstalk->reserve($timeout);
        return new Job($this->getClientName(), $job);
    }

 /* (non-PHPdoc)
     * @see \ORC\API\Beanstalkd\Connection\IConnection::touch()
     */
    public function touch(Job $job)
    {
        return $this->pheanstalk->touch($job->getJob());
    }

 /* (non-PHPdoc)
     * @see \ORC\API\Beanstalkd\Connection\IConnection::useTube()
     */
    public function useTube($tube)
    {
        return $this->pheanstalk->useTube($tube);
    }

 /* (non-PHPdoc)
     * @see \ORC\API\Beanstalkd\Connection\IConnection::watch()
     */
    public function watch($tube)
    {
        return $this->pheanstalk->watch($tube);
    }

}