<?php

namespace Dang\Mvc;

class Request
{
    private static $_instance = null;
    private $_post = array();  //post里的参数
    private $_get = array();  //命令行下/和url里get里的参数都放在这里
    private $_isConsole = 0;  //是否命令行，1是命令行,0非命令行

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
        $_ddargv = array();

        $argv = array();
        if (isset($_SERVER['argv'])) {
            $argv = $_SERVER['argv'];
        }
        if (!is_array($argv)) {
            $argv = array();
        }
        if (count($argv) > 0) {
            $this->_isConsole = 1;
        }
        if ($this->_isConsole == 1) {
            array_shift($argv);
            $queryString = join("&", $argv);
            parse_str($queryString, $_ddargv);
            $this->_get = $_ddargv;
        } else {
            $this->_get = $_GET;
        }
        $this->_post = $_POST;
    }

    /*
     * 获取post参数，如果没有设置，则返回默认值
     */
    public function getParamPost($name, $default = null)
    {
        if (isset($this->_post[$name])) {
            return $this->_post[$name];
        }

        return $default;
    }

    /*
     * 设置post参数值
     * 同时将修改同步到$clean_gp
     */
    public function setParamPost($name, $value)
    {
        $this->_post[$name] = $value;
        return $this;
    }

    /*
     * 检查post里的值是否设置
     */
    public function issetParamPost($name)
    {
        if (isset($this->_post[$name])) {
            return true;
        }

        return false;
    }

    /*
     * 命令行下参数：first=value&arr[]=foo+bar&arr[]=baz
     * 由于命令行下的参数传递和url里的一样，所以将命令行下的参数也归入get里
     */
    public function getParamQuery($name, $default = null)
    {
        if (isset($this->_get[$name])) {
            return $this->_get[$name];
        }

        return $default;
    }

    public function getParamQueries()
    {
        return $this->_get;
    }

    /*
     */
    public function setParamQuery($name, $value)
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

        if ($this->_isConsole == 1) {
            return $default;
        }

        if (isset($this->_post[$name])) {
            return $this->_post[$name];
        }

        return $default;
    }

    /*
     */
    public function setParam($name, $value)
    {
        if ($this->_isConsole == 0) {
            if (isset($this->_post[$name])) {
                $this->_post[$name] = $value;
                return $this;
            }
        }

        $this->_get[$name] = $value;
        return $this;
    }

    /*
     * 检查是否是ajax请求
     */
    public function isAjaxRequest()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }
}
