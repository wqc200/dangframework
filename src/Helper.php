<?php

namespace Dang;

class Helper
{
    private static $_holders = array();
    private static $_routers = array();

    public static function result()
    {
        $result = new \Dang\Logic\Result();
        return $result;
    }

    public static function forwordTo($module = null, $controller = null, $action = null)
    {
        \Dang\Mvc\To::instance()->forwordTo($module, $controller, $action);
    }

    public static function isDevice($device)
    {
        $mobileDetect = new \Detection\MobileDetect();
        if (preg_match("/$device/is", $mobileDetect->getUserAgent())) {
            return true;
        }

        return false;
    }

    public static function isMobile()
    {
        $mobileDetect = new \Detection\MobileDetect();
        if ($mobileDetect->isMobile()) {
            return true;
        }

        return false;
    }

    public static function isTablet()
    {
        $mobileDetect = new \Detection\MobileDetect();
        if ($mobileDetect->isTablet()) {
            return true;
        }

        return false;
    }

    public static function serverUrl()
    {
        return \Dang\Logic\ServerUrl::instance();
    }

    public static function holder($name): \Dang\Logic\Holder
    {
        if (!isset(self::$_holders[$name])) {
            self::$_holders[$name] = new \Dang\Logic\Holder();
        }
        return self::$_holders[$name];
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

    public static function url($params = array(), $route = null)
    {
        if (!$route) {
            $route = self::routeIterator()->getDefault();
        }
        $router = self::routeIterator()->getRouter($route);
        $str = $router->toUrl($params);
        return $str;
    }

    public static function routeIterator(): \Dang\Mvc\RouteIterator
    {
        return \Dang\Mvc\RouteIterator::instance();
    }

    public static function paginationControl(\Dang\Logic\Paginator $paginator, $filename, $requestParams, $route = null)
    {
        $pageParams = $paginator->getParams();
        $params = array_merge($requestParams, $pageParams);

        return self::tpl()->include($filename, $params);
    }
}
