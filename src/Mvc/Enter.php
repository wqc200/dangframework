<?php

namespace Dang\Mvc;

class Enter
{
    /*
     * 构造入口
     */
    function __construct()
    {
        if (isset($_SERVER['REQUEST_URI'])) {
            $request_url = $_SERVER['REQUEST_URI'];
            $router = new \Dang\Mvc\Router();
            $route = $router->fromUrl($request_url);
            \Dang\Mvc\Param::instance()->setRoute($route);
        }

        $module = \Dang\Mvc\Request::instance()->getParamGet("module", "www");
        $module = \Dang\Mvc\Util::paramUrlToMvc($module);
        $this->moduleName = ucfirst($module);
        \Dang\Mvc\Param::instance()->setModule($this->moduleName);

        $device = \Dang\Mvc\Util::paramUrlToMvc($device);
        \Dang\Mvc\Param::instance()->setDevice($device);

        $controller = \Dang\Mvc\Request::instance()->getParamGet("controller", "index");
        $controller = \Dang\Mvc\Util::paramUrlToMvc($controller);
        $this->controllerName = ucfirst($controller);
        \Dang\Mvc\Param::instance()->setController($this->controllerName);

        $action = \Dang\Mvc\Request::instance()->getParamGet("action", "index");
        $action = \Dang\Mvc\Util::paramUrlToMvc($action);
        $this->actionName = ucfirst($action);
        \Dang\Mvc\Param::instance()->setAction($this->actionName);
    }

    //执行器
    public function run()
    {
        //用于forward，最多forward2次，避免进入死循环
        for ($i = 0; $i < 10; $i++) {
            $break = true;

            $classer = "\Controller\\" . $this->moduleName . "\\" . $this->controllerName;
            //实例化控制器
            $controller = new $classer();
            if (!method_exists($controller, $this->actionName)) {
                exit('notFoundAction');
            }
            $controller->$this->actionName();

            //检查mvc参数是否被重写
            if ($this->moduleName != \Dang\Mvc\Param::instance()->getModule()) {
                $break = false;
                $this->moduleName = \Dang\Mvc\Param::instance()->getModule();
            }
            if ($this->controllerName != \Dang\Mvc\Param::instance()->getController()) {
                $break = false;
                $this->controllerName = \Dang\Mvc\Param::instance()->getController();
            }
            if ($this->actionName != \Dang\Mvc\Param::instance()->getAction()) {
                $break = false;
                $this->actionName = \Dang\Mvc\Param::instance()->getAction();
            }

            if ($break == true) {
                break;
            }
        }
    }
}

