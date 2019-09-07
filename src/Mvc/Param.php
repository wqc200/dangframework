<?php

namespace Dang\Mvc;

class Param
{
    protected static $_instance = null;
    
    protected $_module;
    protected $_controller;
    protected $_action;

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

    public function getModule()
    {
        return $this->_module;
    }

    public function setModule($name)
    {
        $this->_module = $name;
        return $this;
    }

    public function getController()
    {
        return $this->_controller;
    }

    public function setController($name)
    {
        $this->_controller = $name;
        return $this;
    }

    public function getAction()
    {
        return $this->_action;
    }

    public function setAction($name)
    {
        $this->_action = $name;
        return $this;
    }
}
