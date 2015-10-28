<?php
namespace ORC\Core\Cache;
/**
 * can be used in ICacher::get and ICacher::update
 * @see ICacher::get
 * @see ICacher::update
 * @author 彦钦
 *
 */
interface ICallback {
    /**
     * @param ICacher/Memcached $cacher
     * @param string $key
     * @param mixed $value notice it's a reference
     * @return bool true if you want to set the value back automatically. With a custom expire time, set the value inside of the method and return false
     */
    public function __invoke($cacher, $key, &$value);
}