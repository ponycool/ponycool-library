<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/12/27
 * Time: 4:08 下午
 */
declare(strict_types=1);

namespace PonyCool\Applet\Utils;

class Json
{
    /**
     * 判断是否是JSON
     * @param $str
     * @return bool
     */
    public static function isJsonStr($str): bool
    {
        if (is_string($str)) {
            @json_decode($str);
            return (json_last_error() === JSON_ERROR_NONE);
        }
        return false;
    }
}