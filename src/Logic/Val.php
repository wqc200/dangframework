<?php

namespace Dang\Logic;

class Val
{
    protected static $_instance = null;

    private $_vars = array();

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

    public static function get($name)
    {
        self::instance()->getVal($name);
    }

    public static function set($name, $value)
    {
        self::instance()->setVal($name, $value);
    }

    public function init()
    {
        $this->_vars = array();
        return $this;
    }

    public function setVal($name, $value)
    {
        $this->_vars[$name] = $value;
        return $this;
    }

    public function getVal($name)
    {
        return $this->_vars[$name];
    }

    public function hasVal($name)
    {
        if (isset($this->_vars[$name])) {
            return true;
        }
        return false;
    }
}
