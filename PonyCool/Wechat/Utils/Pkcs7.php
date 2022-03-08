<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/9/3
 * Time: 10:29 上午
 */
declare(strict_types=1);

namespace PonyCool\Wechat\Utils;

/**
 * 提供基于PKCS7算法的加解密接口
 * Class Pkcs7
 * @package PonyCool\Wechat\Utils
 */
class Pkcs7
{
    public static int $block_size = 32;

    /**
     * 对需要加密的明文进行填充补位
     * @param string $text 需要进行填充补位操作的明文
     * @return string 补齐明文字符串
     */
    function encode(string $text): string
    {
        $block_size = Pkcs7::$block_size;
        $text_length = strlen($text);
        //计算需要填充的位数
        $amount_to_pad = Pkcs7::$block_size - ($text_length % Pkcs7::$block_size);
        if ($amount_to_pad == 0) {
            $amount_to_pad = $block_size;
        }
        //获得补位所用的字符
        $pad_chr = chr($amount_to_pad);
        $tmp = "";
        for ($index = 0; $index < $amount_to_pad; $index++) {
            $tmp .= $pad_chr;
        }
        return $text . $tmp;
    }

    /**
     * 对解密后的明文进行补位删除
     * @param string $text 解密后的明文
     * @return string 删除填充补位后的明文
     */
    function decode(string $text): string
    {

        $pad = ord(substr($text, -1));
        if ($pad < 1 || $pad > 32) {
            $pad = 0;
        }
        return substr($text, 0, (strlen($text) - $pad));
    }
}
