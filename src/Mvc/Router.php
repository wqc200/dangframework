<?php

/*
 * 路由控制器
 *
 * @author wuqingcheng
 * @date 2013.05.22
 * @email wqc200@gmail.com
 */

namespace Dang\Mvc;

class Router
{
    public function __construct()
    {

    }

    public function toUrl($param, $route)
    {
        if(!is_array($param)){
            $param = (array) $param;
        }

        $config = \Dang\Quick::config("route");
        if(isset($config->toUrl->{$route})){
            $objName = $config->toUrl->{$route};
        }else{
            $objName = "\Dang_Mvc_Route_Default";
        }

        $router = new $objName();
        return $router->toUrl($param);
    }

    public function fromUrl($url)
    {
        $config = \Dang\Quick::config("route");
        foreach($config->fromUrl->toArray() as $route=>$rules)
        {
            for($i=0;$i<count($rules);$i++)
            {
                if(preg_match($rules[$i], $url)){
                    $objName = $config->toUrl->{$route};
                    $router = new $objName();
                    $router->fromUrl($url);
                    return $route;
                }
            }
        }

        return "";
    }

}
