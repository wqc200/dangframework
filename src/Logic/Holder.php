<?php

namespace Dang\Logic;

class Holder
{
    private $_items;

    function __construct()
    {
        $this->_items = array();
    }

    public function append($content)
    {
        $this->_items[] = $content;
        return $this;
    }

    public function prepend($content)
    {
        array_unshift($this->_items, $content);
        return $this;
    }

    public function __toString()
    {
        return join("\n", $this->_items);
    }
}
