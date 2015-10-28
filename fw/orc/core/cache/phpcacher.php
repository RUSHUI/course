<?php
namespace ORC\Core\Cache;
use ORC\Util\Util;
class PHPCacher extends CacherBase implements ICacher {
    protected $data = array();
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
        if (isset($this->data[$key])) {
            $this->data[$key]['value'] = intval($this->data[$key]['value']);
        } else {
            $this->data[$key] = array('value' => 0, 'expire' => 0);
        }
        $this->data[$key]['value'] -= $offset;
        return true;
    }

	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::delete()
     */
    public function delete($key)
    {
        if (is_array($key)) {
            foreach ($key as $v) {
                $this->delete($v);
            }
            return true;
        }
        $key = $this->resolveKey($key);
        if(isset($this->data[$key])) {
            unset($this->data[$key]);
        }
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
        $this->data = array();
        return true;
    }

	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::get()
     */
    public function get($key, \ORC\Core\Cache\ICallback $callback = null)
    {
        $key = $this->resolveKey($key);
        $value = isset($this->data[$key]) ? $this->data[$key]['value'] : false;
        if ($value !== false) {
            $expire = $this->data[$key]['expire'];
            if ($expire != 0) {
                if ($expire < Util::getNow()) {
                    unset($this->data[$key]);
                    $value = false;
                }
            }
        }
        if ($value === false && $callback !== null) {
//             if (version_compare(PHP_VERSION, '5.4.0') > 0) {
//                 $result = call_user_func($callback, $this, $key, $value);
//             } else {
                $result = call_user_func_array($callback, array($this, $key, &$value));//has to do this before PHP5.4
//             }
            if ($result) {
                $this->set($key, $value);
            }
        }
        return $value;
    }

	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::getMult()
     */
    public function getMult(array $keys, \ORC\Core\Cache\ICallbackMult $callback = null)
    {
        $clean_keys = $keys;
        $keys = $this->resolveKey($keys);
        $data = array();
        $missing_keys = array();
        foreach ($keys as $key) {
            $value = $this->get($key);
            if ($value === false) {
                $missing_keys[] = $key;
                continue;
            }
            $data[$key] = $value;
        }
        if (count($missing_keys) == 0 || $callback == null) {
            return $data;
        }
        $values = array();
//         if (version_compare(PHP_VERSION, '5.4.0') > 0) {
//             $result = call_user_func($callback, $this, $this->cleanKey($missing_keys), $values);
//         } else {
            $result = call_user_func_array($callback, array($this, $this->cleanKey($missing_keys), &$values));//has to do this before PHP5.4
//         }
        if ($result) {
            //set back to cacher
            foreach ($values as $key => $value) {
                $this->set($key, $value);
            }
        }
        $result = array();
        foreach ($clean_keys as $clean_key) {
            $key = $this->resolveKey($clean_key);
            if (isset($data[$key])) {
                $result[$clean_key] = $data[$key];
            } elseif (isset($values[$clean_key])) {
                $result[$clean_key] = $values[$clean_key];
            }
        }
        return $result;
    }

	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::getAllKeys()
     */
    public function getAllKeys($strip_namespace = true)
    {
        $keys = array_keys($this->data);
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
        $key = $this->resolveKey($key);
        if (isset($this->data[$key])) {
            $this->data[$key]['value'] = intval($this->data[$key]['value']);
        } else {
            $this->data[$key] = array('value' => 0, 'expire' => 0);
        }
        $this->data[$key]['value'] += $offset;
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
        $key = $this->resolveKey($key);
        if ($expiration != 0) {
            $expiration += Util::getNow();
        }
        $this->data[$key] = array('value' => $value, 'expire' => $expiration);
        return true;
    }

	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::setMult()
     */
    public function setMult(array $items, $expiration = 0)
    {
        foreach ($items as $key => $value) {
            $this->set($key, $value, $expiration);
        }
        return true;
    }

	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::touch()
     */
    public function touch($key, $expiration)
    {
        $key = $this->resolveKey($key);
        if (isset($this->data[$key])) {
            if ($expiration != 0) {
                $expiration += Util::getNow();
            }
            $this->data[$key]['expire'] = $expiration;
            return true;
        }
        return false;
    }

	/* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::update()
     */
    public function update($key, \ORC\Core\Cache\ICallback $callback, $expiration = 0)
    {
        //@todo consider to add a optimistic lock
        $value = $this->get($key);
//         if (version_compare(PHP_VERSION, '5.4.0') > 0) {
//             call_user_func($callback, $this, $key, $value);
//         } else {
            call_user_func_array($callback, array($this, $key, &$value)); // has to do this before PHP5.4
//         }
        $this->set($key, $value, $expiration);
        return true;
    }
    
}