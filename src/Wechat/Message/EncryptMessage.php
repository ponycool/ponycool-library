<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/9/3
 * Time: 4:15 下午
 */
declare(strict_types=1);

namespace PonyCool\Wechat\Message;

use PonyCool\Wechat\Utils\{AesCrypt, ErrorCode, Str};
use PonyCool\Wechat\Signature\Signature;
use Exception;

class EncryptMessage
{
    /**
     * 消息加密
     * @param string $msg 明文消息
     * @param string $appId
     * @param string $aesKey
     * @param string $token
     * @return array
     */
    public static function encrypt(string $msg, string $appId, string $aesKey, string $token): array
    {
        try {
            if (is_null($aesKey)) {
                throw new Exception('微信消息加密失败，未配置EncodingAESKey');
            }
            if (is_null($appId)) {
                throw new Exception('微信消息加密失败，未配置AppID');
            }
            $aesCrypt = new AesCrypt($aesKey);
            $encryptRes = $aesCrypt->encrypt($msg, $appId);
            if ($encryptRes[0] !== 0) {
                throw new Exception('微信消息加密失败');
            }
            $encryptMessage = $encryptRes[1];

            //生成签名
            $nonce = Str::getRandomStr();
            $timeStamp = (string)time();
            $signatureRes = Signature::getSHA1($token, $timeStamp, $nonce, $encryptMessage);
            if ($signatureRes[0] !== 0) {
                throw new Exception('微信加密签名生成失败');
            }
            $signature = $signatureRes[1];
            $message = Reply::cryptMsg($encryptMessage, $signature, $timeStamp, $nonce);
            return [0, $message];
        } catch (Exception $e) {
            return [ErrorCode::$EncryptMessage, $e->getMessage()];
        }
    }
}
