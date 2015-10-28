<?php
namespace ORC\MVC;
use ORC\Exception\SystemException;
use ORC\DAO\Table;
use ORC\DAO\Table\DataRow;
use Detection\MobileDetect;
use ORC\Util\Logger;
class Request extends \ORC\Util\AdvancedContainer {
	protected static $_instance;
	
	protected $_base_path;
	protected $_base_url;
	protected $_uri;
	
	protected $_mobile_detect;
	/**
	 * @todo consider to save the objects using by loadObj and loadTable
	 * @var \ORC\MVC\Request\Object
	 */
	protected $_load_objs = array();
	protected $_load_tables = array();
	
	const args = '__args__';
	const refer = '__refer__';
	/**
	 *
	 * @return Request
	 */
	public static function getInstance() {
		if (!isset(self::$_instance) || !is_object(self::$_instance)) {
			self::$_instance = new static();
		}
		return self::$_instance;
	}
	
	protected function __construct() {
		$base_path = $this->getBasePath();
		if (strpos($_SERVER['REQUEST_URI'], $base_path) === 0) {
			$uri = '/' . substr($_SERVER['REQUEST_URI'], strlen($base_path));
		} else {
		    Logger::getInstance('system')->addError(json_encode($_SERVER) . ':' . $base_path);
			throw new \ORC\Exception\SystemException('URL Parse Error');
		}
		$this->_uri = $uri;
		
		if (substr(strtolower($_SERVER['SERVER_PROTOCOL']), 0, 5) == 'https') {
		    $protocol = 'https';
		} else {
		    $protocol = 'http';
		}
		//set main server
		$main_server = $protocol . '://' . $_SERVER['HTTP_HOST'] . $base_path;
		\ORC\Core\Config::getInstance()->set('main_server', $main_server);
		//var_dump($this->getBaseURL(), $this->getBasePath());
	}
	
	public function get($key, $filter_type='safe', $default_value = null) {
// 	    pre(parent::get(self::args));exit();
	    if (is_int($key)) {
	        //load args
	        $findkey = sprintf('%s%d', self::args, $key);
	        if ($this->exists($findkey)) {
	            return parent::get($findkey);
	        } else {
	            $args = parent::get(self::args);
	            if (isset($args[$key])) {
    	            $value = $args[$key];
    	            $value = $this->applyFilter($value, $filter_type, $default_value);
	            } else {
	                $value = null;
	            }
	            return parent::set($findkey, $value);
	        }
	    }
	    if ($this->exists($key)) {
	        return parent::get($key);
	    } else {
	        if (!isset($_REQUEST[$key])) {
	            return parent::set($key, $default_value);
	        }
	        $value = $_REQUEST[$key];
	        $value = $this->applyFilter($value, $filter_type, $default_value);
	        return parent::set($key, $value);
	    }
	}
	
	public function getURI() {
		return $this->_uri;
	}
	
	public function getBasePath() {
		if (!isset($this->_base_path)) {
			$index = $_SERVER['SCRIPT_NAME'];
			if (false !== ($pos = strpos($index, 'index.php'))) {
				$this->_base_path = substr($index, 0, $pos);
			} else {
				$this->_base_path = $index;
			}
		}
		return $this->_base_path;
	}
	
	public function getBaseURL() {
		if (!isset($this->_base_url)) {
			if (substr(strtolower($_SERVER['SERVER_PROTOCOL']), 0, 5) == 'https') {
				$protocol = 'https';
			} else {
				$protocol = 'http';
			}
			$this->_base_url = $protocol . '://' . $_SERVER['HTTP_HOST'] . $this->getBasePath();
		}
		return $this->_base_url;
	}
	
	/**
	 * 
	 * @param \ORC\MVC\Request\Object $obj
	 * @return \ORC\MVC\Request\Object
	 */
	public function loadObj(\ORC\MVC\Request\Object $obj) {
	    //$name = strtolower(get_class($obj));
	    $ref = new \ReflectionObject($obj);
	    $properties = $ref->getProperties(\ReflectionProperty::IS_PUBLIC);
	    foreach ($properties as $property) {
	        $name = $property->getName();
	        //get the doc
	        $doc = $property->getDocComment();
	        $type = $this->parseObjPropDoc($doc);
	        if ($type['default'] == '__empty_string__') {
	            $type['default'] = '';
	        }
	        $value = isset($_REQUEST[$name]) ? $_REQUEST[$name] : null;
	        switch ($type['type']) {
	            case 'enum':
	                $filter = array('enum' => $type['extra']);
	                break;
	            case 'valid':
	                $obj->{$name} = call_user_func($type['extra'], $value);
	                $this->set($name, $obj->{$name});
	                continue 2;
	                break;
	            default:
	                $filter = $type['type'];
	                break;
	        }
	        $this->set($name, $obj->{$name} = $this->applyFilter($value, $filter, $type['default']));
	    }
	    return $obj;
	}
	
	public function loadTable(Table $table, DataRow $dataRow = null) {
	    $schema = $table->getSchema();
	    $fields = $schema['fields'];
	    $obj = new \ORC\MVC\Request\Object();
	    foreach ($fields as $field_name => $field) {
 	        if (!isset($_REQUEST[$field_name])) {
	            continue;
	        }
	        switch ($field['type']) {
	            case 'smallint':
	            case 'int':
	            case 'mediumint':
	                if ($field['unsigned']) {
	                    $filter = 'posint';
	                } else {
	                    $filter = 'int';
	                }
	                break;
	            case 'decimal':
	                $filter = 'numeric';
	                break;
	            case 'enum':
	                $filter = array('enum' => $field['value']);
	                break;
	            default:
	                $filter = 'safe';
	                break;
	        }
	        $default_value = null;
	        if ($dataRow) {
	            if ($dataRow->exists($field_name)) {
	                $default_value = $dataRow->get($field_name);
	            }
	        }
	        $value = $this->applyFilter($_REQUEST[$field_name], $filter, $default_value);
	        $this->set($field_name, $obj->{$field_name} = $value);
	    }
	    return $obj;
	}
	
	public function setReferURL($url = null) {
	    if (empty($url)) {
	        $url = \ORC\Util\Url::getCurrentURL(true);
	    }
	    $this->set(self::refer, $url);
	}
	
	public function getReferURL() {
	    return $this->get(self::refer);
	}
	
	/**
	 * 
	 * @return \Detection\MobileDetect
	 */
	public function getMobileDetector() {
	    if (!isset($this->_mobile_detect)) {
	        $this->_mobile_detect = new MobileDetect();
	    }
	    return $this->_mobile_detect;
	}
	protected function parseObjPropDoc($doc) {
	    $type = $default = $extra = null;
	    $doc = explode(PHP_EOL, $doc);
	    foreach ($doc as $line) {
	        $line = trim($line);
	        if (preg_match('/@type (.*)$/i', $line, $matches)) {
	            //try to get the type
	            $string = trim($matches[1]);
	            if (strpos($string, '(')) {
	                $type = strstr($string, '(', true);
	                if (preg_match('/' . $type . '\((.*)\)/i', $string, $m)) {
	                    $extra = $m[1];
	                }
	            } else {
	                $type = $string;
	            }
	            $type = strtolower(trim($type));
	        } elseif (preg_match('/@default (.*)$/i', $line, $matches)) {
	            $default = $matches[1];
	        }
	    }
	    switch ($type) {
	        case 'enum':
	            eval('$array = array(' . $extra . ');');
	            $extra = $array;
	            break;
	        case 'func'://similiar with enum, use the return values from function to be the avaliable options
	            $result = call_user_func($extra);
	            if (is_array($result)) {
	                $type = 'enum';
	                $extra = $result;
	            } else {
	                throw new SystemException('Unknown type of property');
	            }
	            break;
	        case 'valid':
	            break;
	        default:
	            break;
	    }
	    return array('type' => $type, 'default' => $default, 'extra' => $extra);
	}
	
	protected function applyFilter($value, $filter, $default_value) {
	    if (is_array ( $filter )) {
	        switch (key ( $filter )) {
	            case 'regex' :
	                $pattern = $filter ['regex'];
	                $filter = 'regex';
	                break;
	            case 'enum' :
	                $pattern = $filter ['enum'];
	                $filter = 'enum';
	                break;
	            case 'array' :
	                $filter = $filter ['array'];
	                if (is_array($value)) {
	                    $result = array();
	                    foreach ($value as $k => $v) {
	                        $result[$k] = $this->applyFilter($v, $filter, $default_value);
	                    }
	                    return $result;
	                }
	            default :
	                // nothing...
	                break;
	        }
	    }
	
	    switch ($filter) {
	        case 'posint' :
	            return ctype_digit ( (string)$value ) ? $value : $default_value;
	        case 'int' :
	            return (ctype_digit ( (string)$value ) || ($value[0] == '-' && ctype_digit ( substr ( $value, 1 ) ))) ? $value : $default_value;
	        case 'alpha' :
	            return ctype_alpha ( $value ) ? $value : $default_value;
	        case 'alphanum' :
	            return ctype_alnum ( $value ) ? $value : $default_value;
	        case 'numeric' :
	            return is_numeric ( $value ) ? $value : $default_value;
	        case 'bool' :
	            return $value ? TRUE : FALSE;
	        case 'raw' :
	            return $value;
	        case 'enum' :
	            return in_array ( $value, $pattern ) ? $value : $default_value;
	        case 'regex' :
	            return preg_match ( $pattern, $value ) ? $value : $default_value;
	        case 'date' :
	            //yyyy-mm-dd
	            if ($value == '') {
	                return $value;
	            }
	            $year = substr($value, 0, 4);
	            $month = substr($value, 5, 2);
	            $day = substr($value, 8, 2);
	            return checkdate($month, $day, $year) ? $value : $default_value;
	        case 'safe' :
	        default :
	            //$value = str_replace ( array ('<', '>', '"', "'", '&', '%', '{', '(' ), '', $value );
	            $value = str_replace ( array ('{','}'), '', $value );
	            // set to default value if there is nothing left after filtering
	            return $value ? $value : $default_value;
	    }
	}
}