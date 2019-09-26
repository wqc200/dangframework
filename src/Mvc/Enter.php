<?php

namespace Dang\Mvc;

class Enter
{
    function __construct()
    {
        \Dang\Helper::routeIterator()->addRouter("dang_main", new \Dang\Mvc\Router\Main());
        \Dang\Helper::routeIterator()->addRouter("dang_base", new \Dang\Mvc\Router\Base());

        if (PHP_SAPI != 'cli') {
            $this->_parseUrl();
        }

        $module = \Dang\Mvc\Request::instance()->getQuery("module");
        \Dang\Mvc\To::instance()->setModule($module);

        $controller = \Dang\Mvc\Request::instance()->getQuery("controller");
        \Dang\Mvc\To::instance()->setController($controller);

        $action = \Dang\Mvc\Request::instance()->getQuery("action");
        \Dang\Mvc\To::instance()->setAction($action);
    }

    public function _parseUrl()
    {
        $requestUrl = $_SERVER['REQUEST_URI'];
        $routerIterator = \Dang\Helper::RouteIterator();
        foreach ($routerIterator as $key => $value) {
            $result = $value->fromUrl($requestUrl);
            if ($result) {
                return;
            }
        }
    }

    public function run($maxForword = 10)
    {
        while (true) {
            $forwordTotal = \Dang\Mvc\To::instance()->getForwordTotal();
            if ($forwordTotal > $maxForword) {
                break;
            }
            \Dang\Mvc\To::instance()->resetForword();

            $module = \Dang\Mvc\To::instance()->getModule();
            $controller = \Dang\Mvc\To::instance()->getController();
            $action = \Dang\Mvc\To::instance()->getAction();

            $classer = "\Controller\\" . $module . "\\" . $controller;
            $controller = new $classer();
            if (!method_exists($controller, $action)) {
                throw new \Exception("Action: " . $action . " not found in class " . $classer . "");
            }
            $controller->$action();

            $isForword = \Dang\Mvc\To::instance()->isForword();
            if (!$isForword) {
                break;
            }
            \Dang\Mvc\To::instance()->increaseTotal();
        }
    }
}

