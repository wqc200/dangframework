<?php

namespace Dang\Mvc;

class Enter
{
    function __construct()
    {
    }

    public function initTo()
    {
        $module = \Dang\Mvc\Request::instance()->getParamQuery("module");
        \Dang\Mvc\To::instance()->setModule($module);

        $controller = \Dang\Mvc\Request::instance()->getParamQuery("controller");
        \Dang\Mvc\To::instance()->setController($controller);

        $action = \Dang\Mvc\Request::instance()->getParamQuery("action");
        \Dang\Mvc\To::instance()->setAction($action);
    }

    public function run($maxForword = 10)
    {
        while(true) {
            $isForword = \Dang\Mvc\To::instance()->isForword();
            if (!$isForword) {
                break;
            }

            $forwordTotal = \Dang\Mvc\To::instance()->forwordTotal();
            if ($forwordTotal > $maxForword) {
                break;
            }

            $module = \Dang\Mvc\To::instance()->getModule();
            $controller = \Dang\Mvc\To::instance()->getController();
            $action = \Dang\Mvc\To::instance()->getAction();

            $classer = "\Controller\\" . $module . "\\" . $controller;
            $controller = new $classer();
            if (!method_exists($controller, $action)) {
                throw new \Exception("Action: " . $action . " not found!");
            }
            $controller->$action();
        }
    }
}

