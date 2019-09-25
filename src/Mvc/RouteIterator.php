<?php

namespace Dang\Mvc;

class RouteIterator implements \Iterator
{
    private static $_instance = null;
    private $_routers = array();

    /*
     * 单例模式入口
     */
    public static function instance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function __construct()
    {

    }

    public function addRouter($name, $router)
    {
        $this->_routers[$name] = $router;
    }

    public function getRouter($name): \Dang\Mvc\Router\RouterInterface
    {
        return $this->_routers[$name];
    }

    function rewind()
    {
        return reset($this->_routers);
    }

    function current()
    {
        return current($this->_routers);
    }

    function key()
    {
        return key($this->_routers);
    }

    function next()
    {
        return next($this->_routers);
    }

    function valid()
    {
        return key($this->_routers) !== null;
    }
}
