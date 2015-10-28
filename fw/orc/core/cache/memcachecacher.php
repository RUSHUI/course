<?php
namespace ORC\Core\Cache;
use ORC\Exception\Cache\MemcacheException;
class MemcacheCacher extends CacherBase implements ICacher {
    protected $_servers = array();
    protected $_connected = false;
    protected $_handler;
    
    public function __construct() {
        $this->initDefaultOptions();
    }
	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::addServer()
     */
    public function addServer($host, $port, $weight = 0)
    {
        if ($this->_connected) {
            throw new MemcacheException('you can not add server after cache engine is connected');
        }
        $server = array($host, $port, $weight);
        $key = md5(json_encode($server) . $host);
        if (!isset($this->_servers[$key])) {
            $this->_servers[$key] = $server;
        }
    }

	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::connect()
     */
    public function connect()
    {
        if ($this->_connected) {
            //throw new MemcacheException('Do not connect twice');
            return;
        }
        $m = new \Memcached($this->getHandlerKey());
        $ss = $m->getServerList();
        
        if(isset($this->_options[ICacher::OPT_PREFIX_KEY])) {
            $this->setOption(\Memcached::OPT_PREFIX_KEY, $this->_options[ICacher::OPT_PREFIX_KEY]);
        }
        if (empty($ss)) {
            $options = $this->getOptions();
            unset($options[ICacher::OPT_PREFIX_KEY]);
            $m->setOptions($options);
            $m->addServers(array_values($this->_servers));
        }
        $this->_connected = true;
        $this->_handler = $m;
    }

	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::decreament()
     */
    public function decreament($key, $offset = 1)
    {
        return $this->_handler->decrement($key, $offset);
    }

	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::delete()
     */
    public function delete($key)
    {
        if (is_array($key)) {
            return $this->_handler->deleteMulti($key);
        }
        return $this->_handler->delete($key);
    }

	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::disconnect()
     */
    public function disconnect()
    {
        //do nothing here
        return false;
    }

	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::flush()
     */
    public function flush()
    {
        return $this->_handler->flush();
    }

	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::get()
     */
    public function get($key, \ORC\Core\Cache\ICallback $callback = null)
    {
        if ($callback) {
            return $this->_handler->get($key, $callback);
        }
        return $this->_handler->get($key);
    }

    
	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::getMult()
     */
    public function getMult(array $keys, \ORC\Core\Cache\ICallbackMult $callback = null)
    {
        $data = $this->_handler->getMulti($keys);
        if ($callback == null) {
            return $data;
        }
        $missing_keys = array();
        foreach ($keys as $key) {
            if (!isset($data[$key])) {
                $missing_keys[] = $key;
            }
        }
        if (count($missing_keys) == 0) {
            return $data;
        }
        $values = array();
//         if (version_compare(PHP_VERSION, '5.4.0') > 0) {
//             $result = call_user_func($callback, $this, $missing_keys, $values);
//         } else {
            $result = call_user_func_array($callback, array($this, $missing_keys, &$values));//has to do this before PHP5.4
//         }
        if ($result) {
            //set back to cacher
            foreach ($values as $key => $value) {
                $this->set($key, $value);
            }
        }
        $result = array();
        foreach ($keys as $key) {
            if (isset($data[$key])) {
                $result[$key] = $data[$key];
            } elseif (isset($values[$key])) {
                $result[$key] = $values[$key];
            }
        }
        return $result;
    }

	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::getAllKeys()
     */
    public function getAllKeys($strip_namespace = true)
    {
        $keys = $this->_handler->getAllKeys();
        if ($strip_namespace) {
            $keys = $this->stripNamespace($keys);
        }
        return $keys;
    }

	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::increament()
     */
    public function increament($key, $offset = 1)
    {
        return $this->_handler->increment($key, $offset);
    }

	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::isConnected()
     */
    public function isConnected()
    {
        return $this->_connected;
    }

	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::set()
     */
    public function set($key, $value, $expiration = 0)
    {
        return $this->_handler->set($key, $value, $expiration);
    }

	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::setMult()
     */
    public function setMult(array $items, $expiration = 0)
    {
        return $this->_handler->setMulti($items, $expiration);
    }

	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::setOption()
     */
    public function setOption($option, $value)
    {
        if ($this->_connected) {
            throw new MemcacheException('you can not change option after cache engine is connected');
        }
        $this->_options[$option] = $value;
    }
    
    public function touch($key, $expiration) {
        if (is_array($key)) {
            foreach ($key as $k) {
                $this->_handler->touch($k, $expiration);
            }
            return true;
        }
        return $this->_handler->touch($key, $expiration);
    }
    
	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::update()
     */
    public function update($key, \ORC\Core\Cache\ICallback $callback, $expiration = 0)
    {
        do {
            $cas = null;
            $value = $this->_handler->get($key, null, $cas);
//             if (version_compare(PHP_VERSION, '5.4.0') > 0) {
//                 call_user_func($callback, $this, $key, $value);
//             } else {
                call_user_func_array($callback, array($this, $key, &$value));//has to do this before PHP5.4
//             }
            if ($this->_handler->getResultCode() == \Memcached::RES_NOTFOUND) {
                $this->_handler->add($key, $value, $expiration);
            } else {
                $this->_handler->cas($cas, $key, $value, $expiration);
            }
        } while ($this->_handler->getResultCode() != \Memcached::RES_SUCCESS);
        return true;
    }
    
    protected function getHandlerKey() {
        if ($this->_connected) {
            return null;
        }
        asort($this->_servers);
        asort($this->_options);
        return md5(json_encode($this->_servers)) . md5(json_encode($this->_options));
    }
    
    protected function initDefaultOptions() {
        $this->_options = array(
            \Memcached::OPT_COMPRESSION => true,
            \Memcached::OPT_SERIALIZER => \Memcached::SERIALIZER_PHP,
            \Memcached::OPT_PREFIX_KEY => '',
            \Memcached::OPT_HASH => \Memcached::HASH_DEFAULT,
            \Memcached::OPT_DISTRIBUTION => \Memcached::DISTRIBUTION_MODULA,
            \Memcached::OPT_LIBKETAMA_COMPATIBLE => false,
            \Memcached::OPT_BUFFER_WRITES => false,
            \Memcached::OPT_BINARY_PROTOCOL => false,
            \Memcached::OPT_NO_BLOCK => false,
            \Memcached::OPT_TCP_NODELAY => false,
            \Memcached::OPT_CONNECT_TIMEOUT => 1000,
            \Memcached::OPT_RETRY_TIMEOUT => 10,//can not be 0, dunno why
            \Memcached::OPT_SEND_TIMEOUT => 0,
            \Memcached::OPT_RECV_TIMEOUT => 0,
            \Memcached::OPT_POLL_TIMEOUT => 1000,
            #\Memcached::OPT_CACHE_LOOKUPS => false,
            #\Memcached::OPT_SERVER_FAILURE_LIMIT => 0
        #http://docs.libmemcached.org/memcached_behavior.html?highlight=cache_lookups#MEMCACHED_BEHAVIOR_CACHE_LOOKUPS
        );
    }
}