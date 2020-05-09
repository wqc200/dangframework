<?php

namespace Dang\Mvc;

class Enter
{
    function __construct()
    {
        \Dang\Helper::routeIterator()->addRouter("dang_main", new \Dang\Mvc\Router\Main());
        \Dang\Helper::routeIterator()->addRouter("dang_base", new \Dang\Mvc\Router\Base());

        if (PHP_SAPI != 'cli') {
            $result = $this->parseUrl();
            if (!$result) {
                return;
            }
        }

        $module = \Dang\Mvc\Request::instance()->getQuery("module");
        \Dang\Mvc\To::instance()->setModule($module);

        $controller = \Dang\Mvc\Request::instance()->getQuery("controller");
        \Dang\Mvc\To::instance()->setController($controller);

        $action = \Dang\Mvc\Request::instance()->getQuery("action");
        \Dang\Mvc\To::instance()->setAction($action);
    }

    private function parseUrl()
    {
        $requestUrl = $_SERVER['REQUEST_URI'];
        $routerIterator = \Dang\Helper::RouteIterator();
        foreach ($routerIterator as $key => $value) {
            if ($key == "dang_main") {
                continue;
            }
            $result = $value->fromUrl($requestUrl);
            if ($result) {
                return true;
            }
        }

        return false;
    }

    public function run($maxForword = 10)
    {
        while (true) {
            $forwordTotal = \Dang\Mvc\To::instance()->getForwordTotal();
            if ($forwordTotal > $maxForword) {
                break;
            }

            $this->runController();

            $isForword = \Dang\Mvc\To::instance()->isForword();
            if (!$isForword) {
                break;
            }
            \Dang\Mvc\To::instance()->unForword();
        }
    }

    public function runController()
    {
        $module = \Dang\Mvc\To::instance()->getModule();
        $controller = \Dang\Mvc\To::instance()->getController();
        $action = \Dang\Mvc\To::instance()->getAction();

        $classer = "\Controller\\" . $module . "\\" . $controller;
        $controller = new $classer();
        $isForword = \Dang\Mvc\To::instance()->isForword();
        if ($isForword) {
            return;
        }
        if (!method_exists($controller, $action)) {
            throw new \Exception("Action: " . $action . " not found in class " . $classer . "");
        }
        $controller->$action();
    }
}

