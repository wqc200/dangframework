<?php

namespace Dang\Logic;

class Paginator
{
    /*
     * 每页默认显示的item个数
     */
    protected static $defaultItemCountPerPage = 10;
    /*
     * 总页数
     */
    protected $pageCount = null;
    /*
     * 充许最大展示的页数
     */
    protected $maxPageCount = 100;
    /*
     * 当前页
     */
    protected $currentPageNumber = 1;

    protected $totalItemCount;

    protected $_filename;
    protected $_route = null;


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

    public function setQuery($requestParam)
    {
        $this->_requestParam = $requestParam;
        return $this;
    }

    public function getTotalItemCount()
    {
        return $this->totalItemCount;
    }

    public function setTotal($totalItemCount)
    {
        $this->totalItemCount = $totalItemCount;
        return $this;
    }

    public function getItemCountPerPage()
    {
        if (empty($this->itemCountPerPage)) {
            $this->itemCountPerPage = self::$defaultItemCountPerPage;
        }

        return $this->itemCountPerPage;
    }

    public function setItemCountPerPage($itemCountPerPage = -1)
    {
        $this->itemCountPerPage = (integer)$itemCountPerPage;
        if ($this->itemCountPerPage < 1) {
            $this->itemCountPerPage = $this->getTotalItemCount();
        }

        return $this;
    }

    public function setMaxPageCount($pageCount)
    {
        $this->maxPageCount = $pageCount;
        return $this;
    }

    public function getOffset()
    {
        $nummber = $this->getItemCountPerPage();
        $offset = ($this->getCurrentPageNumber() - 1) * $nummber;
        return $offset;
    }

    public function getPageTotal()
    {
        if (!$this->pageCount) {
            $this->pageCount = (integer)ceil($this->getTotalItemCount() / $this->getItemCountPerPage());
        }
        if ($this->maxPageCount && $this->maxPageCount < $this->pageCount) {
            $this->pageCount = $this->maxPageCount;
        }

        return $this->pageCount;
    }

    public function getParams()
    {
        $pageCount = $this->getPageTotal();
        $currentPageNumber = $this->getCurrentPageNumber();

        $first = 1;
        $current = $currentPageNumber;
        $last = $pageCount;

        // Previous and next
        if ($currentPageNumber - 1 > 0) {
            $previous = $currentPageNumber - 1;
        } else {
            $previous = 0;
        }

        if ($currentPageNumber + 1 <= $pageCount) {
            $next = $currentPageNumber + 1;
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
            "requestQuery" => $this->_requestParam,
        );
    }

    public function normalizePageNumber($pageNumber)
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

    public function getCurrentPageNumber()
    {
        return $this->normalizePageNumber($this->currentPageNumber);
    }

    public function setCurrent($pageNumber)
    {
        $this->currentPageNumber = (integer)$pageNumber;
        return $this;
    }
}
