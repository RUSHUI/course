<?php
namespace ORC\Core\Cache;
abstract class CacherBase {
    protected $_options = array();
    
    /* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::getOption()
     */
    public function getOption($option)
    {
        return isset($this->_options[$option]) ? $this->_options[$option] : null;
    }
    
    /* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::getOptions()
     */
    public function getOptions()
    {
        return $this->_options;
    }
    
    /* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::setOption()
     */
    public function setOption($option, $value)
    {
        $this->_options[$option] = $value;
    }
    
    public function getDelay($key, ICallback $callback = null, $delay = 10, $retry = 2) {
        if ($retry < 1) {
            return $this->get($key, $callback);        
        }
        $data = $this->get($key);
        
        while ($data === false) {
            $retry --;
            usleep($delay);
            if ($retry == 0) {
                $data = $this->get($key, $callback);
                return $data;
            }
            $data = $this->get($key);
        }
        return $data;
    }
    
    /* (non-PHPdoc)
     * @see \ORC\Core\Cache\ICacher::deleteAll()
     */
    public function deleteAll() {
        $keys = $this->getAllKeys(true);
        foreach ($keys as $key) {
            $this->delete($key);
        }
        return true;
    }
    
    protected function cleanKey($keys) {
        if (is_array($keys)) {
            foreach ($keys as $index => $key) {
                $keys[$index] = $this->cleanKey($key);
            }
            return $keys;
        }
        $prefix = $this->getOption(ICacher::OPT_PREFIX_KEY);
        if ($prefix) {
            return substr($keys, strlen($prefix));
        } else {
            return $keys;
        }
    }
    
    protected function stripNamespace($key) {
        $prefix = $this->getOption(ICacher::OPT_PREFIX_KEY);
        if (!$prefix) {
            return $key;
        }
        if (is_array($key)) {
            $new_keys = array();
            foreach ($key as $k => $v) {
                $new_key = $this->stripNamespace($v);
                if ($new_key !== null) {
                    $new_keys[] = $new_key;
                }
            }
            return $new_keys;
        }
        if (substr($key, 0, strlen($prefix)) == $prefix) {
            return substr($key, strlen($prefix));
        }
        return null;
    }
    
    protected function cleanValues(array $values) {
        $result = array();
        foreach ($values as $key => $value) {
            $result[$this->cleanKey($key)] = $value;
        }
        return $result;
    }
    protected function resolveKey($key) {
        $prefix = $this->getOption(ICacher::OPT_PREFIX_KEY);
        if ($prefix) {
            if (is_array($key)) {
                foreach ($key as $k => $v) {
                    $key[$k] = $this->resolveKey($v);
                }
                return $key;
            } else {
                return sprintf('%s%s', $prefix, $key);
            }
        }
        return $key;
    }
}