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

            $classer = "Controller_" . $this->moduleName . "_" . $this->controllerName;
            //实例化控制器
            $controller = new $classer();
            //执行 $method 方法之前所需要进行的操作，如“打开数据库连接”
            $controller->preDispatch();
            //执行 $method 方法
            $result = $controller->onDispatch();
            //执行 $method 方法之后所需要进行的操作，如“关闭数据库连接”
            $controller->postDispatch();

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

        //根据返回的model对象的类型，决定使用什么样的输出方法
        if ($result instanceof Dang_Mvc_View_Model_XmlModel) {
            $actionModel = $result;

            $filename = Dang_Mvc_Template::instance()->setExtension("pxml")->getActionFilename();
            $actionModel->setTemplate($filename);

            /*
        $module = Dang_Mvc_Template::instance()->getModule();
        $controller = Dang_Mvc_Template::instance()->getController();
        $action = Dang_Mvc_Template::instance()->getAction();
        Dang_Mvc_Template::instance()->setExtension("pxml");

        //获取method里的html代码
        $viewXmlModel = $result;
        $path = "./tpl/".$module."/".$controller;
        $viewXmlModel->setTemplatePath($path);
        $viewXmlModel->setTemplateName($action);
        */

            $phpRenderer = new Dang_Mvc_PhpRenderer();
            $content = $phpRenderer->renderModel($actionModel);

            header("content-type: application/xml; charset=UTF-8");
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo $content;

        } elseif ($result instanceof Dang_Mvc_View_Model_TxtModel) {
            $txtModel = $result;
            $filename = Dang_Mvc_Template::instance()->setExtension("ptxt")->getActionFilename();
            $txtModel->setTemplate($filename);
            $phpRenderer = new Dang_Mvc_PhpRenderer();
            $content = $phpRenderer->renderModel($txtModel);
            echo $content;

        } elseif ($result instanceof Dang_Mvc_View_Model_JsonModel) {
            $callback = \Dang\Mvc\Request::instance()->getParam("callback");
            if ($callback) {
                echo $callback . "(" . json_encode($result->getVariables()) . ")";
            } else {
                echo json_encode($result->getVariables());
            }

        } elseif ($result instanceof Dang_Mvc_View_Model_JsonpModel) {
            echo "jsonpCallback(" . json_encode($result->getVariables()) . ")";

        } elseif ($result instanceof Dang_Mvc_View_Model_HtmlModel) {
            $actionModel = $result;

            $filename = Dang_Mvc_Template::instance()->getActionFilename();
            $actionModel->setTemplate($filename);
            $actionModel->setCaptureTo('content');

            $layoutModel = Dang_Mvc_ServiceManager::instance()->get("layoutModel");
            $filename = Dang_Mvc_Template::instance()->getLayoutFilename();
            $layoutModel->setTemplate($filename);
            $layoutModel->addChild($actionModel, 'content');
            $view = new Dang_Mvc_View_View();
            $content = $view->render($layoutModel);

            echo $content;

        } elseif ($result instanceof Dang_Mvc_View_Model_NoneModel) {
            //do nothing

        } else {
            echo "\n Error: no model \n";

        }
    }
}

