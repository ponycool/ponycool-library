<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/9/3
 * Time: 10:39 上午
 */
declare(strict_types=1);

namespace PonyCool\Wechat\Utils;

class Str
{
    /**
     * 随机生成16位字符串
     * @return string 生成的字符串
     * @author Pony
     */
    public static function getRandomStr(): string
    {
        $str = "";
        $str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($str_pol) - 1;
        for ($i = 0; $i < 16; $i++) {
            $str .= $str_pol[mt_rand(0, $max)];
        }
        return $str;
    }
}
