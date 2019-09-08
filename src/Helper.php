<?php

namespace Dang;

class Helper
{
    private static $_holderNames = array();

    public static function isDevice($device)
    {
        $mobileDetect = new \Detection\MobileDetect();
        if(preg_match("/$device/is", $mobileDetect->getUserAgent())){
            return true;
        }

        return false;
    }

    public static function isMobile()
    {
        $mobileDetect = new \Detection\MobileDetect();
        if($mobileDetect->isMobile()){
            return true;
        }

        return false;
    }

    public static function isTablet()
    {
        $mobileDetect = new \Detection\MobileDetect();
        if($mobileDetect->isTablet()){
            return true;
        }

        return false;
    }

    public static function url()
    {
        return \Dang\Logic\ServerUrl::instance();
    }

    public static function holder($name):\Dang\Logic\Holder
    {
        if (!isset(self::$_holderNames[$name])) {
            self::$_holderNames[$name] = new \Dang\Logic\Holder();
        }
        return self::$_holderNames[$name];
    }

    public static function tpl()
    {
        return \Dang\Logic\Tpl::instance();
    }

    public static function log($dirName)
    {
        return \Dang\Logic\Log::instance($dirName);
    }

    public static function val()
    {
        return \Dang\Logic\Val::instance();
    }
}
