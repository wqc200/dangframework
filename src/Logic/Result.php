<?php

namespace Dang\Logic;


class Result
{
    private $_errorCode;
    private $_message;
    private $_data;

    function __construct()
    {
    }

    function setErrorCode($errorCode = 0){
        $this->_errorCode = $errorCode;
    }

    function getErrorCode(){
        return $this->_errorCode;
    }

    function setMessage($message = null){
        $this->_message = $message;
    }

    function getMessage(){
        return $this->_message;
    }

    function setData($data){
        $this->_data = $data;
    }

    function getData(){
        return $this->_data;
    }
}