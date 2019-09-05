<?php

namespace Dang\Helper;

class PlaceHolder
{
    private $_items;

    function __construct()
    {
        $this->_items = array();
    }

    public function append($content)
    {
        $this->_items[] = $content;
    }

    public function prepend($content)
    {
        array_unshift($this->_items, $content);
    }

    public function __toString()
    {
        return join("\n", $this->_items);
    }
}
