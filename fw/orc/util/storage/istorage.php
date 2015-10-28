<?php
namespace ORC\Util\Storage;
interface IStorage {
    /**
     * @param string $namespace
     */
    public function __construct($namespace = '');
    /**
     * @param string $key
     * @param string $value
     */
    public function set($key, $value);
    /**
     * @param string $key
     */
    public function get($key);
}