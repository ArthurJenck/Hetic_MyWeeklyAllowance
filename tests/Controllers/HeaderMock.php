<?php

namespace App\Controllers;

if (!function_exists('App\Controllers\header')) {
    function header(string $string)
    {
        global $mockHeaders;
        $mockHeaders[] = $string;
    }
}

if (!function_exists('App\Controllers\setcookie')) {
    function setcookie($name, $value = "", $expires = 0, $path = "", $domain = "", $secure = false, $httponly = false)
    {
        $_COOKIE[$name] = $value;
        return true;
    }
}
