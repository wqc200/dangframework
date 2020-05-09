<?php

namespace Dang\Logic;


class Result
{
    public $errorCode;
    public $message;
    public $data;
    private $_callback;

    function __construct()
    {
    }

    function __toString()
    {
        $result = json_encode($this, JSON_UNESCAPED_UNICODE);
        if ($this->getCallback()) {
            return $this->getCallback() . "(" . $result . ")";
        } else {
            return $result;
        }
    }

    function setErrorCode($errorCode = 0){
        $this->errorCode = $errorCode;
        return $this;
    }

    function getErrorCode(){
        return $this->errorCode;
    }

    function setMessage($message = null){
        $this->message = $message;
        return $this;
    }

    function getMessage(){
        return $this->message;
    }

    function setData($data = null){
        $this->data = $data;
        return $this;
    }

    function getData(){
        return $this->data;
    }

    function setCallback($callback = null){
        $this->_callback = $callback;
        return $this;
    }

    function getCallback(){
        return $this->_callback;
    }
}