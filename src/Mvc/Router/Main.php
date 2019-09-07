<?php

namespace Dang\Mvc\Router;

class Main implements RouterInterface
{
    public function __construct()
    {
    }

    public function toUrl($param)
    {
        if(!is_array($param)){
            $param = (array) $param;
        }

        unset($param['module']);
        unset($param['controller']);
        unset($param['action']);

        $serverUrl = \Dang\Helper::serverUrl();

        $str = \Dang\Mvc\Util::appendParams($param);
        if($str){
            $serverUrl .= "/?".$str;
        }
        return $serverUrl;
    }

    public function fromUrl($url):bool
    {
        if(preg_match("/^\/(index.php)?$/si", $url, $match)){
            \Dang\Mvc\Request::instance()->setParamQuery("module", "www");
            \Dang\Mvc\Request::instance()->setParamQuery("controller", "main");
            \Dang\Mvc\Request::instance()->setParamQuery("action", "index");

            return true;
        }

        return false;
    }
}