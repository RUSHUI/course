<?php
namespace ORC\Helpers;
class Html {
	public static function text($name, $default_value = null, $extra_attributes = array()) {
		
	}
	
	public static function select($name, Array $options, $default_value = '', $attributes = '', $id = '') {
	    $output = '<select name="' . $name . '"';
		if ($id != '') {
			$output .= ' id="' . $id . '"';
		}
		if (is_array($attributes)) {
		    $temp = $attributes;
		    $attributes = array();
		    foreach ($temp as $k => $v) {
		        if (is_int($k)) {
		            $attributes[] = $v;
		        } else {
		            $attributes[] = sprintf('%s="%s"', $k, $v);
		        }
		    }
		    $attributes = implode(' ', $attributes);
		}
		if ($attributes) {
			$output .= ' ' . $attributes;
		}
		$output .= ">\n";
		foreach ($options as $value => $option) {
			$selected = '';
			if (is_array($default_value)) {
				if (in_array($value, $default_value)) {
					$selected = ' selected';
				}
			} else {
				if ($value == $default_value) {
					$selected = ' selected';
				}
			}
			$output .= sprintf("\t<option value=\"%s\"%s>%s</option>\n",
				$value, $selected, $option
				);
		}
		$output .= "</select>\n";
		return $output;
	}
	
	public static function radio($name, Array $values, $default_value = '', $attributes = '') {
		$output = '';
		foreach ($values as $value => $title) {
			if ($value == $default_value) {
				$checked = ' checked ';
			} else {
				$checked = ' ';
			}
			$output .= sprintf('<input type="radio" name="%s" value="%s"%s%s />%s',
					$name,  $value, $checked, $attributes, $title
			);
		}
		return $output;
	}
	
	public static function checkbox($name, Array $values, array $default_values = array(), $attributes = '') {
	    $output = '';
	    foreach ($values as $value => $title) {
	        if (in_array($value, $default_values)) {
	            $checked = ' checked ';
	        } else {
	            $checked = ' ';
	        }
	        $output .= sprintf('<input type="checkbox" name="%s" value="%s"%s%s />%s',
	            $name,  $value, $checked, $attributes, $title
	        );
	    }
	    return $output;
	}
	
	public static function displayEmail($email, $record = null) {
		return sprintf('<a href="mailto:%s">%s</a>', $email, $email);
	}
}