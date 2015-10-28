<?php
namespace ORC\MVC\Request;
use ORC\DAO\Table\DataRow;
class Object {
    public function __get($k) {
        if (property_exists($this, $k)) {
            return $this->{$k};
        }
        return null;
    }
    
    public function __call($name, array $arguments) {
        if (strcasecmp('get', $name) == 0) {
            $key = array_pop($arguments);
            if (property_exists($this, $key)) {
                return $this->{$key};
            }
            return null;
        }
    }
    
    public function prepareDataRow(DataRow $data = null, array $properties = null) {
        if ($properties == null) {
            $properties = array();
            $ref = new \ReflectionObject($this);
            $ps = $ref->getProperties(\ReflectionProperty::IS_PUBLIC);
            foreach ($ps as $p) {
                $properties[] = $p->getName();
            }
        }
        if ($data == null) {
            $data = new DataRow();
        }
        foreach ($properties as $property) {
            $data->set($property, $this->{$property});
        }
        return $data;
    }
}