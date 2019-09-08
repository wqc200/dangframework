<?php

namespace Dang\Mvc\Router;

class Base implements RouterInterface
{
    public function __construct()
    {
    }

    public function toUrl($param)
    {
        if(!is_array($param)){
            $param = (array) $param;
        }

        $module = \Dang\Mvc\Router::instance()->getModule();
        $controller = \Dang\Mvc\Router::instance()->getController();
        $action = \Dang\Mvc\Router::instance()->getAction();

        $query = $param;
        if(isset($param['module'])){
            $module = $param['module'];
            unset($query['module']);
        }
        if(isset($param['controller'])){
            $controller = $param['controller'];
            unset($query['controller']);
        }
        if(isset($param['action'])){
            $action = $param['action'];
            unset($query['action']);
        }

        $module = \Dang\Mvc\Util::paramMvcToUrl($module);
        $controller = \Dang\Mvc\Util::paramMvcToUrl($controller);
        $action = \Dang\Mvc\Util::paramMvcToUrl($action);

        $serverUrl = \Dang\Helper::url()->getPreUrl();

        $url = $serverUrl."/".$module."/".$controller."/".$action;
        $str = \Dang\Mvc\Util::appendParams($query);
        if($str){
            $url .= "/?".$str;
        }
        return $url;
    }

    public function fromUrl($url):bool
    {
        if(preg_match("/^\/([a-z0-9-_]+)\/([a-z0-9-_]+)\/([a-z0-9-_]+)[\/]?[\?]?/si", $url, $match)){
            $module = $match['1'];
            $controller = $match['2'];
            $action = $match['3'];

            \Dang\Mvc\Request::instance()->setParamQuery("module", $module);
            \Dang\Mvc\Request::instance()->setParamQuery("controller", $controller);
            \Dang\Mvc\Request::instance()->setParamQuery("action", $action);

            return true;
        }

        return false;
    }
}
