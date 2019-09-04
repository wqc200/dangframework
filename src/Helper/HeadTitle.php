<?php

namespace Dang\Helper;

class HeadTitle
{
    protected static $_instance = null;
    private $_items;

    public static function instance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __toString()
    {
        return join(" - ", $this->_items);
    }
    
    public function append($title)
    {
        $this->_items[] = $title;
        
        return $this;
    }
    
    public function prepend($title)
    {
        array_unshift($this->_items, $title);
        
        return $this;
    }
}
