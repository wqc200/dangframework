<?php

namespace Dang\Logic;

class Curl
{
    //把常见错误转换成中文提示
    private static $errros = array(
        6 => '服务器网络不通！',
        28 => '下载网页超时！',
    );


    private $headers;
	private $user_agent;
	private $compression;
	private $cookies;
	private $cookie_file;
	private $proxy;

    public $errno;
    public $error;
    public $errorMsg;
    public $infos;
    public $status;

    private $_debug;

    function __construct($compression = 'gzip')
    {
        $this->headers[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg';
        $this->headers[] = 'Connection: Keep-Alive';
        $this->headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
        $this->user_agent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1';
        $this->compression = $compression;
    }

    public function addHeader($header)
    {
        $this->headers[] = $header;
    }

    function proxy($ip, $port)
    {
        $this->proxy = TRUE;
        $this->proxyIp = $ip;
        $this->proxyPort = $port;
    }

    function infos()
    {
        /*
         * http://us3.php.net/curl_errno
         *
         * 如果没有发生错误，是curl_errno返回0
         */
        $errno = curl_errno($this->process);
        $this->errno = $errno;
        $this->error = curl_error($this->process);
        if (isset(self::$errros[$errno])) {
            $this->error = self::$errros[$errno];
        }

        $this->infos = curl_getinfo($this->process);
        $this->status = $this->infos['http_code'];
    }

    function cookie($cookie_file = "./cookies.txt")
    {
        $this->cookies = True;

        if (file_exists($cookie_file)) {
            $this->cookie_file = $cookie_file;
        } else {
            fopen($cookie_file, 'w') or $this->error('The cookie file could not be opened. Make sure this directory has the correct permissions');
            $this->cookie_file = $cookie_file;
            fclose($this->cookie_file);
        }
    }

    function get($url)
    {
        $process = $this->process = curl_init($url);
        curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($process, CURLOPT_HEADER, 0);
        curl_setopt($process, CURLOPT_USERAGENT, $this->user_agent);
        if ($this->cookies == TRUE) curl_setopt($process, CURLOPT_COOKIEFILE, $this->cookie_file);
        if ($this->cookies == TRUE) curl_setopt($process, CURLOPT_COOKIEJAR, $this->cookie_file);
        curl_setopt($process, CURLOPT_ENCODING, $this->compression);
        //毫秒超时
        //curl_setopt($process, CURLOPT_TIMEOUT_MS, 100);
        curl_setopt($process, CURLOPT_TIMEOUT, 6);
        if ($this->proxy) curl_setopt($process, CURLOPT_PROXY, $this->proxyIp . ':' . $this->proxyPort);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($process, CURLOPT_MAXREDIRS, 100);
        $return = curl_exec($process);
        $this->infos();
        curl_close($process);
        return $return;
    }

    function post($url, $data)
    {
        if (is_array($data)) {
            $fields = array_map("urlencode", $data);
            $data = '';
            //url-ify the data for the POST
            foreach ($fields as $key => $value) {
                $data .= $key . '=' . $value . '&';
            }
            rtrim($data, '&');
        }

        $process = $this->process = curl_init($url);
        curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($process, CURLOPT_HEADER, 1);
        curl_setopt($process, CURLOPT_USERAGENT, $this->user_agent);
        if ($this->cookies == TRUE) curl_setopt($process, CURLOPT_COOKIEFILE, $this->cookie_file);
        if ($this->cookies == TRUE) curl_setopt($process, CURLOPT_COOKIEJAR, $this->cookie_file);
        curl_setopt($process, CURLOPT_ENCODING, $this->compression);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        if ($this->proxy) curl_setopt($cUrl, CURLOPT_PROXY, 'proxy_ip:proxy_port');
        curl_setopt($process, CURLOPT_POSTFIELDS, $data);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($process, CURLOPT_POST, 1);
        $return = curl_exec($process);
        $this->infos();
        curl_close($process);
        return $return;
    }

    function error($error)
    {
		throw new \Exception(sprintf(
			"Curl error '%s'",
			$error
		));
    }
}
