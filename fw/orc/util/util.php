<?php
namespace ORC\Util;
final class Util {
	public static function getNow() {
		return time();
	}
	
	public static function getMemUsage() {
		$mem = memory_get_usage(true);
		if ($mem > 1024) {
			$k = intval($mem / 1024);
			$b = $mem - 1024 * $k;
		}
		if ($k > 1024) {
			$m = (int)($k / 1024);
			$k = $k - 1024 * $m;
			return sprintf('%dM%dK%dB', $m, $k, $b);
		} else {
			return sprintf('%dK%dB', $k, $b);
		}
	}
	
	/**
	 * @param int $length
	 * @param string $type
	 */
	public static function generateRandStr($length, $type = null) {
	    switch ($type) {
	        case 'alpha':
	            $str = 'abcdefghijlkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	            break;
	        case 'alphanum':
	        default:
	            $str = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	            break;
	        case 'num':
	            $str = '0123456789';
	            break;
	        case 'loweralpha':
	            $str = 'abcdefghijklmnopqrstuvwxyz';
	            break;
	        case 'loweralphanum':
	            $str = 'abcdefghijklmnopqrstuvwxyz0123456789';
	            break;
	    }
	    $result = '';
	    $max_length = strlen($str) - 1;
	    for ($i = 0; $i < $length; $i ++) {
	        $result .= $str[mt_rand(0, $max_length)];
	    }
	    return $result;
	}
	
	public static function getIP() {
	    $ip = false;
	    if (! empty ( $_SERVER ["HTTP_CLIENT_IP"] )) {
	        $ip = $_SERVER ["HTTP_CLIENT_IP"];
	    }
	    if (! empty ( $_SERVER ['HTTP_X_FORWARDED_FOR'] )) {
	        $ips = explode ( ", ", $_SERVER ['HTTP_X_FORWARDED_FOR'] );
	        if ($ip) {
	            array_unshift ( $ips, $ip );
	            $ip = FALSE;
	        }
	        for($i = 0; $i < count ( $ips ); $i ++) {
	            if (! eregi ( "^(10|172\.16|192\.168)\.", $ips [$i] )) {
	                $ip = $ips [$i];
	                break;
	            }
	        }
	    }
	    return ($ip ? $ip : $_SERVER ['REMOTE_ADDR']);
	}
	
	/**
	 * 判断一个ip是否在一段ip内
	 * 目前只支持ipv4
	 * @param string $ip 需要判断的ip
	 * @param string $start_ip 开始的ip
	 * @param string $end_ip 结束ip
	 * @return boolean
	 */
	public static function inValidIPRange($ip, $start_ip, $end_ip) {
	    $ip_start = sprintf('%u', ip2long($start_ip));
	    $ip_end = sprintf('%u', ip2long($end_ip));
	    $strip = sprintf('%u', ip2long($ip));
	    if ($strip >= $ip_start && $strip <= $ip_end) {
	        return true;
	    }
	    return false;
	}
}