<?php

namespace Dang\Mysql;

class Expression{
    protected $_expression;

    public function __construct($expression)
    {
        $this->_expression = (string) $expression;
    }

    public function __toString()
    {
        return $this->_expression;
    }
}
