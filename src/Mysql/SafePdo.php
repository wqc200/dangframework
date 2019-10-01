<?php

namespace Dang\Mysql;

Class SafePdo extends \PDO
{
    /*
     * 如果使用 throw new \Exception 会出现错误：Exception thrown without a stack frame in Unknown on line 0
     *
     */
    public static function exception_handler($exception)
    {
        die('Uncaught exception: ' . $exception->getMessage());
    }

    /*
     * 如果应用程序不在 PDO 构造函数中捕获异常，zend 引擎采取的默认动作是结束脚本并显示一个回溯跟踪，此回溯跟踪可能泄漏完整的数据库连接细节，包括用户名和密码。因此有责任去显式（通过 catch 语句）或隐式（通过 set_exception_handler() ）地捕获异常。
     *
     * 参考：http://php.net/manual/zh/pdo.connections.php
     */
    public function __construct($dsn, $username = '', $password = '', $driver_options = array())
    {
        set_exception_handler(array(__CLASS__, 'exception_handler'));
        parent::__construct($dsn, $username, $password, $driver_options);
        restore_exception_handler();
    }
}
