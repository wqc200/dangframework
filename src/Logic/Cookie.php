<?php

namespace Dang\Logic;

class Cookie
{
    function __construct($cookie_pre, $domain = "")
    {
        $this->_cookiePre = $cookie_pre;

        //http://us2.php.net/setcookie
        if (!empty($domain)) {
            // Fix the domain to accept domains with and without 'www.'.
            if (strtolower(substr($domain, 0, 4)) == 'www.') $domain = substr($domain, 4);
            // Add the dot prefix to ensure compatibility with subdomains
            if (substr($domain, 0, 1) != '.') $domain = '.' . $domain;

            // Remove port information.
            $port = strpos($domain, ':');

            if ($port !== false) $domain = substr($domain, 0, $port);
        }
        $this->_domain = $domain;
    }

    public function getCookie($var)
    {
        $cookie_pre = $this->_cookiePre;

        $MY_COOKIE = array();

        $prelength = strlen($cookie_pre);
        foreach ($_COOKIE as $key => $val) {
            if (substr($key, 0, $prelength) == $cookie_pre) {
                $MY_COOKIE[(substr($key, $prelength))] = $val;
            }
        }

        if (isset($MY_COOKIE[$var])) {
            return $MY_COOKIE[$var];
        }

        return false;
    }

    public function setCookie($var, $value, $life = 0)
    {
        $cookie_pre = $this->_cookiePre;
        $cookie_domain = $this->_domain;
        $cookie_path = '/';

        setcookie($cookie_pre . $var, $value,
            $life ? time() + $life : 0, $cookie_path,
            $cookie_domain, $_SERVER['SERVER_PORT'] == 443 ? 1 : 0);
    }

    public function delCookie($var)
    {
        $this->setCookie($var, "", 0);
    }
}
