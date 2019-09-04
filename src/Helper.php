<?php

namespace Dang;

class Helper
{
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

    public static function headTitle()
    {
        return \Dang\Helper\HeadTitle::instance();
    }
}
