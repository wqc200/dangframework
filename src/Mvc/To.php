<?php

namespace Dang\Mvc;

class To
{
    private static $_instance = null;
    private $_module;
    private $_controller;
    private $_action;
    private $_isChanged = false;
    private $_changeTotal;

    /*
     * 单例模式入口
     */
    public static function instance()
    {
        if(self::$_instance == null){
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function __construct()
    {
    }

    public function getModule()
    {
        return $this->_module;
    }

    public function isForword()
    {
        return $this->_isChanged;
    }

    public function forwordTotal()
    {
        return $this->_changeTotal;
    }

    public function setModule($name)
    {
        if ($name == null) {
            return $this;
        }

        $name = \Dang\Mvc\Util::paramUrlToMvc($name);
        $this->_module = $name;
        $this->_isChanged = true;
        return $this;
    }

    public function getController()
    {
        return $this->_controller;
    }

    public function setController($name)
    {
        if ($name == null) {
            return $this;
        }

        $name = \Dang\Mvc\Util::paramUrlToMvc($name);
        $this->_controller = $name;
        $this->_isChanged = true;
        return $this;
    }

    public function getAction()
    {
        return $this->_action;
    }

    public function setAction($name)
    {
        if ($name == null) {
            return $this;
        }

        $name = \Dang\Mvc\Util::paramUrlToMvc($name);
        $this->_action = $name;
        $this->_isChanged = true;
        return $this;
    }
}
