<?php

namespace Dang\Logic;

use Memcached as MemcachedResource;

class Dmemcached
{
    protected $_memcached;

    private $_debug;

    public function __construct($host, $port, $username = null, $password = null)
    {
        $memcached = new MemcachedResource("ocs");

        //检查是否已经生成了长连接
        if (count($memcached->getServerList()) == 0) {
            $memcached->setOption(MemcachedResource::OPT_COMPRESSION, true);
            $memcached->setOption(MemcachedResource::OPT_DISTRIBUTION, MemcachedResource::DISTRIBUTION_CONSISTENT);
            $memcached->setOption(MemcachedResource::OPT_LIBKETAMA_COMPATIBLE, true);
            $memcached->setOption(MemcachedResource::OPT_BINARY_PROTOCOL, true);

            $memcached->addServer($host, $port);

            //支持sasl功能
            if ($username != null && $password != null) {
                $memcached->setSaslAuthData($username, $password);
            }
        }

        $this->_memcached = $memcached;
    }

    public function getItems($keys)
    {
        $memc = $this->_memcached;
        $result = $memc->getMulti($keys, $cas, MemcachedResource::GET_PRESERVE_ORDER);

        return $result;
    }

    /**
     * 可以 是一个Unix时间戳（自1970年1月1日起至失效时间的整型秒数），或者是一个从现在算起的以秒为单位的数字。
     * 对于后一种情况，这个 秒数不能超过60×60×24×30（30天时间的秒数）;
     * 如果失效的值大于这个值， 服务端会将其作为一个真实的Unix时间戳来处理而不是 自当前时间的偏移。
     * @param array $items
     * @param inter $expiration
     * @return bool
     */
    public function setItems($items, $expiration = 0)
    {
        $memc = $this->_memcached;
        $result = $memc->setMulti($items, $expiration);

        return $result;
    }

    public function getItem(& $normalizedKey, & $success = null)
    {
        $memc = $this->_memcached;
        $result = $memc->get($normalizedKey);

        $success = true;
        if ($result === false || $result === null) {
            $rsCode = $memc->getResultCode();
            if ($rsCode == MemcachedResource::RES_NOTFOUND) {
                $result = null;
                $success = false;
            } elseif ($rsCode) {
                $success = false;
            }
        }

        return $result;
    }

    public function delItem($normalizedKey)
    {
        $memc = $this->_memcached;
        if (!$memc->delete($normalizedKey)) {
            return false;
        }

        return true;
    }

    public function setItem(& $normalizedKey, & $value, $expiration = 0)
    {
        $memc = $this->_memcached;
        if (!$memc->set($normalizedKey, $value, $expiration)) {
            return false;
        }

        return true;
    }

    public function incrementItem(& $normalizedKey, $offset = 1, $expiration = 0)
    {
        $memc = $this->_memcached;
        if (!$memc->increment($normalizedKey, $offset)) {
            $memc->set($normalizedKey, $offset, $expiration);
        }

        return true;
    }

    public function flush()
    {
        $memc = $this->_memcached;
        return $memc->flush();
    }
}

