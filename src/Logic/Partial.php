<?php

namespace Dang\Logic;

class Partial
{
    protected $_vars;
    protected $_path;
    protected $_extension;

    function __construct()
    {
    }

    public function __get($name)
    {
        return $this->_vars[$name];
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

    public function setVars($variables)
    {
        $this->_vars = $variables;
        return $this;
    }

    public function include($file, $variable = null)
    {
        $render = new \Dang\Logic\Tpl();
        $render->render($file, $variable);
    }

    public function partial($file, $variable = null)
    {
        $render = new \Dang\Logic\Partial();
        $render->render($file, $variable);
    }

    public function render($file, $variable = null)
    {
        if (substr($file, 0, 1) == DIRECTORY_SEPARATOR) {
            $filename = $file . "." . $this->getExtension();
        } else {
            $filename = (string)$this->getPath() . "/" . $file . "." . $this->getExtension();
        }

        if (!file_exists($filename)) {
            throw new \Exception("tpl file: " . $filename . " not found!");
        }

        $this->setVars($variable);
        if ($variable != null) {
            extract($variable);
        }

        include $filename;
    }
}
