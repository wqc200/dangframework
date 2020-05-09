<?php

namespace Dang\Logic;

class Tpl
{
    protected static $_instance = null;
    protected $_vars;
    protected $_path;
    protected $_extension;

    public static function instance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    function __construct()
    {
    }

    public function setPath($path)
    {
        $this->_path = (string)$path;
        return $this;
    }

    public function getPath()
    {
        if (isset($this->_path)) {
            return $this->_path;
        }

        $tplDir = "./tpl";
        $this->_path = (string)$tplDir;
        return $this->_path;
    }

    public function setExtension($ext)
    {
        $this->_extension = (string)$ext;
        return $this;
    }

    public function getExtension()
    {
        if ($this->_extension) {
            return $this->_extension;
        }

        $this->_extension = "phtml";
        return $this->_extension;
    }

    public function partial($file, $variable = null)
    {
        $render = new \Dang\Logic\Partial();
        $render->render($file, $variable);
    }

    public function include($file, $variable = null)
    {
        if (substr($file, 0, 1) == DIRECTORY_SEPARATOR) {
            $filename = $file . "." . $this->getExtension();
        } else {
            $filename = (string)$this->getPath() . "/" . $file . "." . $this->getExtension();
        }

        if (!file_exists($filename)) {
            throw new \Exception("tpl file: " . $filename . " not found!");
        }

        if ($variable != null) {
            extract($variable);
        }

        include $filename;
    }
}
