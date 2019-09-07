<?php

namespace Dang;

class Utility
{
    public static function toXml($variable)
    {
        $xml = "<xml>";
        foreach ($variable as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";

        return $xml;
    }

    public static function random($length, $numeric = 0)
    {
        PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
        if ($numeric) {
            $hash = sprintf('%0' . $length . 'd', mt_rand(0, pow(10, $length) - 1));
        } else {
            $hash = '';
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
            $max = strlen($chars) - 1;
            for ($i = 0; $i < $length; $i++) {
                $hash .= $chars[mt_rand(0, $max)];
            }
        }
        return $hash;
    }

    public static function md5sumPath($md5sum, $stringLenth = 24, $splitLenth = 3)
    {
        $filePath = "/" . join('/', str_split(substr($md5sum, 0, $stringLenth), $splitLenth)) . "/";

        return $filePath;
    }

    public static function headerRedirect($url)
    {
        header('Location: ' . $url);
        exit;
    }
}
