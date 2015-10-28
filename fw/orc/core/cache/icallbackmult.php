<?php
namespace ORC\Core\Cache;
interface ICallbackMult {
    public function __invoke(ICacher $cacher, array $keys, array &$values);
}