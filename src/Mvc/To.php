<?php

namespace Dang\Mvc;

class To
{
    private static $_instance = null;
    private $_module;
    private $_controller;
    private $_action;
    private $_forword = false;
    private $_total = 0;

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
        return $this->_forword;
    }

    public function getForwordTotal()
    {
        return $this->_total;
    }

    public function resetForword()
    {
        $this->_forword = false;
        return $this;
    }

    public function increaseTotal()
    {
        $this->_total++;
    }

    public function forwordTo($module = null, $controller = null, $action = null)
    {
        $this->_forword = true;

        if ($module != null) {
            $this->setModule($module);
        }

        if ($controller != null) {
            $this->setController($controller);
        }

        if ($action != null) {
            $this->setAction($action);
        }

        return $this;
    }

    public function setModule($name)
    {
        if ($name == null) {
            throw new \Exception("Mvc router error: module can not be null!");
        }

        $name = \Dang\Mvc\Util::paramUrlToMvc($name);
        $this->_module = $name;
        return $this;
    }

    public function getController()
    {
        return $this->_controller;
    }

    public function setController($name)
    {
        if ($name == null) {
            throw new \Exception("Mvc router error: controller can not be null!");
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
            throw new \Exception("Mvc router error: action can not be null!");
        }

        $name = \Dang\Mvc\Util::paramUrlToMvc($name);
        $this->_action = $name;
        $this->_isChanged = true;
        return $this;
    }
}
