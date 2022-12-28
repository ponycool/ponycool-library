<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/9/9
 * Time: 10:29 下午
 */
declare(strict_types=1);

namespace PonyCool\Applet\Signature;

class Signature
{
    /**
     * 获取签名
     * @param string $rawData
     * @param string $sessionKey
     * @return string
     */
    public static function get(string $rawData, string $sessionKey): string
    {
        return sha1($rawData . $sessionKey);
    }
}