<?php

namespace Dang\Mvc;

class Autoloader
{
    private $_namespacePath = array();
    private $_defaultPath = array();

    public function register()
    {
        if (function_exists('__autoload')) {
            //    Register any existing autoloader function with SPL, so we don't get any clashes
            spl_autoload_register('__autoload');
        }

        spl_autoload_register(array($this, "load"));
        return $this;
    }

    /*
     * 根据namespace设置自动加载的路径
     * @param $namespace 命名空间
     * @path 路径
     */
    public function namespacePath($namespace, $path)
    {
        $this->_namespacePath[strtolower($namespace)] = $path;
        return $this;
    }

    public function defaultPath($path)
    {
        $this->_defaultPath = $path;
        return $this;
    }

    /*
     * 根据命名空间加载相应的include path
     */
    private function load($className)
    {
        if (class_exists($className, false) || interface_exists($className, false)) {
            return false;
        }

        //设置默认值
        $path = $this->_defaultPath;

        preg_match("/^[\\\]?([a-z]+)[\\\]?/si", $className, $m);
        if ($m) {
            $namespace = strtolower($m[1]);

            //查看定义的namespace 和 path
            if (key_exists($namespace, $this->_namespacePath)) {
                $path = $this->_namespacePath[$namespace];
            }
        }

        $filename = realpath($path) . "/" . preg_replace('/[\\\]/', DIRECTORY_SEPARATOR, $className) . '.php';
        if (!file_exists($filename)) {
            return false;
        }

        return include $filename;
    }
}

