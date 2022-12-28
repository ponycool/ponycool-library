<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/8/25
 * Time: 8:44 下午
 */
declare(strict_types=1);

namespace PonyCool\Wechat\Signature;

use PonyCool\Wechat\Utils\ErrorCode;
use Exception;

class Signature
{
    /**
     * 校验签名
     * @param string $token
     * @param string|null $encrypt_msg
     * @return bool
     */
    public static function verify(string $token, string $encrypt_msg = null): bool
    {
        try {
            $signature = $_GET["signature"] ?? null;
            $timestamp = $_GET["timestamp"] ?? null;
            $nonce = isset($_GET["timestamp"]) ? $_GET["nonce"] : null;
            $tmpArr = array($token, $timestamp, $nonce);
            if (!is_null($encrypt_msg)) {
                array_push($tmpArr, $encrypt_msg);
                $signature = $_GET["msg_signature"] ?: null;
            }
            sort($tmpArr, SORT_STRING);
            $tmpStr = implode($tmpArr);
            $tmpStr = sha1($tmpStr);
            if ($tmpStr == $signature) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 用SHA1算法生成安全签名
     * @param string $token 票据
     * @param string $timestamp 时间戳
     * @param string $nonce 随机字符串
     * @param string $encrypt_msg 密文消息
     * @return array
     */
    public static function getSHA1(string $token, string $timestamp, string $nonce, string $encrypt_msg): array
    {
        //排序
        try {
            $array = array($encrypt_msg, $token, $timestamp, $nonce);
            sort($array, SORT_STRING);
            $str = implode($array);
            return array(ErrorCode::$OK, sha1($str));
        } catch (Exception $e) {
            return array(ErrorCode::$ComputeSignatureError, null);
        }
    }
}
