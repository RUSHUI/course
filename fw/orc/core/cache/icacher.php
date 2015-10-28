<?php
namespace ORC\Core\Cache;
interface ICacher {
    const EMPTY_VALUE = '__empty_value__';
    
    const OPT_PREFIX_KEY = 'orc_icacher_opt_prefix_key';
    
    /**
     * notice you can't call this function when cache server is connected
     * @param string $host
     * @param int $port
     * @param int $weight
     */
    public function addServer($host, $port, $weight = 0);
    
    public function connect();
    
    /**
     * disconnect from cache server. Usually this is not necessary
     */
    public function disconnect();
    
    /**
     * @return bool
     */
    public function isConnected();
    
    public function setOption($option, $value);
    
    /**
     * @param mixed $option
     * @return mixed null if option doesn't exists
     */
    public function getOption($option);
    
    public function getOptions();
    
    /**
     * @return mixed, false if not exists
     * @param string $key
     * @param ICallback callback function called when key doesn't exists
     */
    public function get($key, ICallback $callback = null);
    
    public function getMult(array $keys, ICallbackMult $callback = null);
    
    /**
     * try $retry times before using callback or return data
     * to avoid concurrency problem
     * @param string $key
     * @param ICallback $callback
     * @param int $delay in millsecond
     * @param int $retry
     */
    public function getDelay($key, ICallback $callback = null, $delay = 10, $retry = 2);
    
    /**
     * 
     * @param string $key
     * @param mixed $value can not store bool, use 0 or 1 instead
     * @param int $expiration expire time
     * @return bool true or false
     */
    public function set($key, $value, $expiration = 0);
    
    public function setMult(array $items, $expiration = 0);
    
    /**
     * update the value, use Optimistic Lock to make sure the option is atom
     * @param string $key
     * @param callable $callback
     * @param int $expiration expire time
     * @return bool
     */
    public function update($key, ICallback $callback, $expiration = 0);
    
    /**
     * 
     * @param string/array $key
     * @return bool
     */
    public function delete($key);
    
    public function increament($key, $offset = 1);
    
    public function decreament($key, $offset = 1);
    
    public function touch($key, $expiration);
    
    /**
     * @return get all available keys
     * @param bool $strip_namespace if set to false, all keys will be returned. if set to true, only that keys with namespace will be returned, and the key will have namespace stripped
     */
    public function getAllKeys($strip_namespace = true);
    
    /**
     * be careful to use this because for some certain cache engine, it'll clear all cache data
     */
    public function flush();
    
    /**
     * only delete the keys in current namespace.
     * It's not as fast or as reliable as flush
     */
    public function deleteAll();
}