<?php
namespace ORC\Util;
class Text {
	public static function string2blob($string) {
		return gzdeflate($string);
	}
	
	public static function blob2string($blob) {
		return gzinflate($blob);
	}
}