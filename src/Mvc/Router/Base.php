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

        $module = \Dang\Mvc\Param::instance()->getModule();
        $controller = \Dang\Mvc\Param::instance()->getController();
        $action = \Dang\Mvc\Param::instance()->getAction();

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

        $serverUrl = \Dang\Helper::serverUrl();

        $url = $serverUrl."/".$module."/".$controller."/".$action;
        $str = \Dang\Mvc\Util::appendParams($query);
        if($str){
            $url .= "/?".$str;
        }
        return $url;
    }

    public function fromUrl($url):bool
    {
        if(preg_match("/^\/(index.php)?$/si", $url, $match)){
            \Dang\Mvc\Request::instance()->setParamGet("module", "www");
            \Dang\Mvc\Request::instance()->setParamGet("controller", "index");
            \Dang\Mvc\Request::instance()->setParamGet("action", "index");

            return true;
        }elseif(preg_match("/^\/([a-z0-9-_]+)\/([a-z0-9-_]+)\/([a-z0-9-_]+)[\/]?[\?]?/si", $url, $match)){
            $module = $match['1'];
            $controller = $match['2'];
            $action = $match['3'];

            \Dang\Mvc\Request::instance()->setParamGet("module", $module);
            \Dang\Mvc\Request::instance()->setParamGet("controller", $controller);
            \Dang\Mvc\Request::instance()->setParamGet("action", $action);

            return true;
        }

        return false;
    }
}
