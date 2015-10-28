<?php
namespace ORC\Core\Cache;
class FakeCacher extends CacherBase implements ICacher {
	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::addServer()
     */
    public function addServer($host, $port, $weight = 0)
    {
        return true;
    }

	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::connect()
     */
    public function connect()
    {
        return true;
    }

	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::decreament()
     */
    public function decreament($key, $offset = 1)
    {
        return true;
    }

	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::delete()
     */
    public function delete($key)
    {
        return true;
    }

	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::disconnect()
     */
    public function disconnect()
    {
        return true;
    }

	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::flush()
     */
    public function flush()
    {
        return true;
    }

	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::get()
     */
    public function get($key, \ORC\Core\Cache\ICallback $callback = null)
    {
        $value = false;
        if ($callback) {
//             if (version_compare(PHP_VERSION, '5.4.0') > 0) {
//                 $result = call_user_func($callback, $this, $key, $value);
//             } else {
                $result = call_user_func_array($callback, array($this, $key, &$value));//has to do this before PHP5.4
//             }
        }
        return $value;
    }

	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::getMult()
     */
    public function getMult(array $keys, \ORC\Core\Cache\ICallbackMult $callback = null)
    {
        $values = array();
        if ($callback) {
//             if (version_compare(PHP_VERSION, '5.4.0') > 0) {
//                 $result = call_user_func($callback, $this, $keys, $values);
//             } else {
                $result = call_user_func_array($callback, array($this, $keys, &$values));//has to do this before PHP5.4
//             }
        }
        return $values;
    }

	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::getAllKeys()
     */
    public function getAllKeys($strip_namespace = true)
    {
        return array();
    }

	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::increament()
     */
    public function increament($key, $offset = 1)
    {
        return true;
    }

	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::isConnected()
     */
    public function isConnected()
    {
        return true;
    }

	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::set()
     */
    public function set($key, $value, $expiration = 0)
    {
        return true;
    }

	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::setMult()
     */
    public function setMult(array $items, $expiration = 0)
    {
        return true;
    }

	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::touch()
     */
    public function touch($key, $expiration)
    {
        return true;
    }

	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::update()
     */
    public function update($key, \ORC\Core\Cache\ICallback $callback, $expiration = 0)
    {
        return true;
    }
    
}