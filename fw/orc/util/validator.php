<?php
namespace ORC\Util;
class Validator {
    public static function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    public static function isValidIP($ip) {
        return filter_var($ip, FILTER_VALIDATE_IP);
    }
    
    public static function isValidMobile($mobile) {
        if (strlen($mobile) != 11) {
            return false;
        }
        return preg_match('/^1[34578]{1}\d{9}$/', $mobile);
    }
}