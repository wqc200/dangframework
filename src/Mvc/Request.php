<?php

namespace Dang\Mvc;

class Request
{
    private static $_instance = null;
    private $_post = array();  //post里的参数
    private $_get = array();  //命令行下/和url里get里的参数都放在这里

    /*
     * 单例模式入口
     */
    public static function instance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /*
     * 命令行下的执行结果:
     * $str = "php /path/index.php first=value&arr[]=foo+bar&arr[]=baz";
     * echo $output['first'];  // value
     * echo $output['arr'][0]; // foo bar
     * echo $output['arr'][1]; // baz
     * 命令行模式下$this->_get 指向 $argv
     */
    public function __construct()
    {
        if (PHP_SAPI == "cli") {
            $argv = array();
            if (isset($_SERVER['argv'])) {
                $argv = $_SERVER['argv'];
            }
            if (!is_array($argv)) {
                $argv = array();
            }
            array_shift($argv);
            $queryString = join("&", $argv);
            $argv = array();
            parse_str($queryString, $argv);
            $this->_get = $argv;
        } else {
            $this->_get = $_GET;
            $this->_post = $_POST;
        }
    }

    /*
     * 获取post参数，如果没有设置，则返回默认值
     */
    public function getPost($name)
    {
        if (isset($this->_post[$name])) {
            return $this->_post[$name];
        }

        return null;
    }

    /*
     * 设置post参数值
     * 同时将修改同步到$clean_gp
     */
    public function setPost($name, $value)
    {
        $this->_post[$name] = $value;
        return $this;
    }

    /*
     * 检查post里的值是否设置
     */
    public function issetPost($name)
    {
        if (isset($this->_post[$name])) {
            return true;
        }

        return false;
    }

    /*
     * 命令行下参数：first=value&arr[]=foo+bar&arr[]=baz
     * 由于命令行下的参数传递和url query里的一样，所以将命令行下的参数也归入get里
     */
    public function getQuery($name)
    {
        if (isset($this->_get[$name])) {
            return $this->_get[$name];
        }

        return null;
    }

    public function allQuery()
    {
        return $this->_get;
    }

    /*
     */
    public function setQuery($name, $value)
    {
        $this->_get[$name] = $value;
        return $this;
    }

    /*
     * 获取参数, 依次获取 get post里的值，如果没有找到则返回默认值
     */
    public function getParam($name, $default = null)
    {
        if (isset($this->_get[$name])) {
            return $this->_get[$name];
        }

        if (isset($this->_post[$name])) {
            return $this->_post[$name];
        }

        return $default;
    }

    /*
     * 检查是否是ajax请求
     */
    public function isAjaxRequest()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }
}
