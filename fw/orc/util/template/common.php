<?php
namespace ORC\Util\Template;
use ORC\Exception\TemplateException;
abstract class Common {
    protected function createItem($item) {
        $item = strtolower($item);
        list($module_name, $type, ) = explode('.', $item, 3);
        $class_name = __NAMESPACE__ . sprintf("\\Item\\%sItem", $type);
        if (class_exists($class_name)) {
            $obj = new $class_name($item);
        }
        if ($obj instanceof \ORC\Util\Template\Item\Item) {
            return $obj;
        }
        throw new TemplateException('unknow template item type');
    }
}