<?php

namespace Dang\Logic;

class Paginator
{
    protected $_number = 12;
    protected $_pageTotal;
    protected $_maxPage = 100;
    protected $_current = 1;
    protected $_itemTotal;
    protected $_filename;
    protected $_route = null;
    protected $_query;

    public function __construct()
    {
    }

    public function __toString()
    {
        $params = $this->getParams();
        ob_start();
        \Dang\Helper::tpl()->include($this->_filename, $params);
        $content = ob_get_contents();
        ob_clean();
        return $content;
    }

    public function setFilename($filename)
    {
        $this->_filename = $filename;
        return $this;
    }

    public function setRoute($route)
    {
        $this->_route = $route;
        return $this;
    }

    public function setQuery($query)
    {
        $this->_query = $query;
        return $this;
    }

    public function getItemTotal()
    {
        return $this->_itemTotal;
    }

    public function setItemTotal($totalItemCount)
    {
        $this->_itemTotal = $totalItemCount;
        return $this;
    }

    public function getNumber()
    {
        return $this->_number;
    }

    public function setNumber($number = -1)
    {
        $this->_number = (integer)$number;
        if ($this->_number < 1) {
            $this->_number = $this->getItemTotal();
        }
        return $this;
    }

    public function setMaxPage($maxPage)
    {
        $this->_maxPage = $maxPage;
        return $this;
    }

    public function getOffset()
    {
        $nummber = $this->getNumber();
        $offset = ($this->getCurrent() - 1) * $nummber;
        return $offset;
    }

    public function getPageTotal()
    {
        if (!$this->_pageTotal) {
            $this->_pageTotal = (integer)ceil($this->getItemTotal() / $this->getNumber());
        }
        if ($this->_maxPage && $this->_maxPage < $this->_pageTotal) {
            $this->_pageTotal = $this->_maxPage;
        }

        return $this->_pageTotal;
    }

    public function getParams()
    {
        $pageCount = $this->getPageTotal();
        $current = $this->getCurrent();

        $first = 1;
        $last = $pageCount;

        // Previous and next
        if ($current - 1 > 0) {
            $previous = $current - 1;
        } else {
            $previous = 0;
        }

        if ($current + 1 <= $pageCount) {
            $next = $current + 1;
        } else {
            $next = 0;
        }

        return array(
            "first" => $first,
            "next" => $next,
            "current" => $current,
            "previous" => $previous,
            "last" => $last,
            "route" => $this->_route,
            "query" => $this->_query,
        );
    }

    private function _normalizePageNumber($pageNumber)
    {
        $pageNumber = (integer)$pageNumber;

        if ($pageNumber < 1) {
            $pageNumber = 1;
        }

        $pageCount = $this->getPageTotal();

        if ($pageCount > 0 && $pageNumber > $pageCount) {
            $pageNumber = $pageCount;
        }

        return $pageNumber;
    }

    public function getCurrent()
    {
        return $this->_normalizePageNumber($this->_current);
    }

    public function setCurrent($pageNumber)
    {
        $this->_current = (integer)$pageNumber;
        return $this;
    }
}
