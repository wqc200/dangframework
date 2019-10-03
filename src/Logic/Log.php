<?php

namespace Dang\Logic;

class Log
{
    public static $_instance;

    private $_filename;
    private $_addTrace;

    /*
     * 单例方法
     */
    public static function instance($dirName)
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self($dirName);
        }

        return self::$_instance;
    }

    public function __construct($dirName)
    {
        $rootDir = rtrim(LOG_DIR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (!is_readable($rootDir)) {
            mkdir($rootDir, 0777);
        }

        $dir = $rootDir . $dirName;
        if (!is_readable($dir)) {
            mkdir($dir, 0777);
        }

        $today = date("Y-m-d");

        $filename = $dir . DIRECTORY_SEPARATOR . $today . ".log";
        if (!is_file($filename)) {
            touch($filename);
        }

        if (!is_readable($filename)) {
            throw new \Exception(sprintf(
                "File '%s' not readable",
                $filename
            ));
        }

        $this->_filename = $filename;
    }

    public function addTrace($add = true)
    {
        $this->_addTrace = $add;

        return $this;
    }

    public function write($type, $content)
    {
        if (!is_string($content)) {
            $content = var_export($content, true);
        }

        $content = '[' . date("Y-m-d H:i:s") . '][' . $type . '] ' . $content;
        if ($this->_addTrace) {
            $content .= "\n" . $this->get_debug_backtrace();
        }
        $content .= "\n";

        $filename = $this->_filename;

        $handle = fopen($filename, "a+");
        fwrite($handle, $content);  //写log
        fclose($handle);  //收尾
    }

    private function get_debug_backtrace()
    {
        $rows = array();

        $traces = debug_backtrace();
        foreach ($traces as $trace) {
            $file = $trace['file'];
            $line = $trace['line'];
            $function = $trace['function'];

            $row = $file . " (" . $function . ")" . " (" . $line . ")";
            $rows[] = $row;
        }

        return join("\n", $rows);
    }

    public function info($content)
    {
        $this->write("info", $content);
    }

    public function debug($content)
    {
        $this->write("debug", $content);
    }

    public function warn($content)
    {
        $this->write("warn", $content);
    }

    public function error($content)
    {
        $this->write("error", $content);
    }
}
