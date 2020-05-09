<?php

namespace Dang\Mvc;

abstract class ControllerAbstract
{

    //构造函数
    function __construct()
    {

    }

    /*
     * action不存在的时候执行的方法
     */
    public function notFoundAction()
    {
        $action = Dang_Mvc_Param::instance()->getAction();
        header("HTTP/1.0 404 Not Found");
        $action = htmlspecialchars($action);
        exit("Action: \"{$action}\" not found!\n");
    }

    /*
     * 请求分发之后需要执行的代码
     * 可以用于打开数据库等类似场景
     */
    public function preDispatch()
    {

    }

    /*
     * 执行请求
     */
    public function onDispatch()
    {
        $action = Dang_Mvc_Param::instance()->getAction();
        $method = $action."Action";
        if (!method_exists($this, $method)) {
            $method = 'notFoundAction';
        }

        \Dang\Clock::getOne('Total')->start();
        $result = $this->$method();
        \Dang\Clock::getOne('Total')->end();

        return $result;
    }

    /*
     * 请求分发之后需要执行的代码
     * 可以用于关闭数据库等类似场景
     */
    public function postDispatch()
    {

    }

    //转向器
    public function forward($params = array())
    {
        if(isset($params['module'])){
            $module = $params['module'];
            $module = Dang_Mvc_Utility::paramUrlToMvc($module);
            $module = ucfirst($module);
            Dang_Mvc_Param::instance()->setModule($module);
        }

        if(isset($params['controller'])){
            $controller = $params['controller'];
            $controller = Dang_Mvc_Utility::paramUrlToMvc($controller);
            $controller = ucfirst($controller);
            Dang_Mvc_Param::instance()->setController($controller);
        }

        if(isset($params['action'])){
            $action = $params['action'];
            $action = Dang_Mvc_Utility::paramUrlToMvc($action);
            $action = ucfirst($action);
            Dang_Mvc_Param::instance()->setAction($action);
        }
    }

    /*
     * 视图助手
     *
     * 使用方法：
     * echo $this->getHelper()->serverUrl();
     */
    public function getHelper()
    {
        $helper = new Dang_Mvc_View_Helper();

        return $helper;
    }
}

?>
