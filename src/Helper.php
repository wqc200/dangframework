<?php

namespace Dang;

class Helper
{
    private static $_placeHolderNames = array();

    public static function isDevice($device)
    {
        $mobileDetect = new \Detection\MobileDetect();
        if(preg_match("/$device/is", $mobileDetect->getUserAgent())){
            return true;
        }

        return false;
    }

    public static function serverUrl($requestUri = null)
    {
        $serverUrl = new \Dang\Helper\ServerUrl();
        return $serverUrl->get($requestUri);
    }

    public static function placeHolder($name):\Dang\Helper\PlaceHolder
    {
        if (!isset(self::$_placeHolderNames[$name])) {
            self::$_placeHolderNames[$name] = new \Dang\Helper\PlaceHolder();
        }
        return self::$_placeHolderNames[$name];
    }

    public static function tpl()
    {
        return \Dang\Helper\Tpl::instance();
    }
}
