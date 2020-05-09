<?php

namespace Dang\Logic;

class Holder
{
    private $_joner;
    private $_items;

    function __construct()
    {
        $this->_items = array();
    }

    public function start()
    {
        ob_start();
    }

    public function end()
    {
        $content = ob_get_clean();
        $this->append($content);
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

    public function joiner($joner)
    {
        $this->_joner = $joner;
        return $this;
    }

    public function __toString()
    {
        return join($this->_joner, $this->_items);
    }
}
