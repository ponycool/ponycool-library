<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/9/3
 * Time: 11:32 上午
 */
declare(strict_types=1);

namespace PonyCool\Wechat\Utils;

use Exception;

class AesCrypt
{
    public $key;

    public function __construct($k)
    {
        $this->key = base64_decode($k . "=");
    }

    /**
     * 对明文进行加密
     * @param string $text 需要加密的明文
     * @param string $appId
     * @return array
     */
    public function encrypt(string $text, string $appId): array
    {
        try {
            // 获得16位随机字符串，填充到明文之前
            $random = Str::getRandomStr();
            $text = $random . pack("N", strlen($text)) . $text . $appId;
            $iv = substr($this->key, 0, 16);
            // 使用自定义的填充方式对明文进行补位填充
            $pkc_encoder = new Pkcs7();
            $text = $pkc_encoder->encode($text);
            // 加密
            $encrypted = openssl_encrypt($text, 'AES-256-CBC', $this->key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);

            // 使用BASE64对加密后的字符串进行编码
            return array(ErrorCode::$OK, base64_encode($encrypted));
        } catch (Exception $e) {
            return array(ErrorCode::$EncryptAESError, null);
        }
    }

    /**
     * 解密
     * @param string $encrypted
     * @param string $appId
     * @return array|string
     */
    public function decrypt(string $encrypted, string $appId)
    {
        try {
            // 使用BASE64对需要解密的字符串进行解码
            $ciphertext_dec = base64_decode($encrypted);
            $iv = substr($this->key, 0, 16);

            // 解密
            $decrypted = openssl_decrypt($ciphertext_dec, 'AES-256-CBC', $this->key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);
        } catch (Exception $e) {
            return array(ErrorCode::$DecryptAESError, null);
        }

        try {
            //去除补位字符
            $pkc_encoder = new Pkcs7();
            $result = $pkc_encoder->decode($decrypted);
            //去除16位随机字符串,网络字节序和AppId
            if (strlen($result) < 16)
                return "";
            $content = substr($result, 16, strlen($result));
            $len_list = unpack("N", substr($content, 0, 4));
            $xml_len = $len_list[1];
            $xml_content = substr($content, 4, $xml_len);
            $from_appId = substr($content, $xml_len + 4);
        } catch (Exception $e) {
            return array(ErrorCode::$IllegalBuffer, null);
        }
        if ($from_appId != $appId)
            return array(ErrorCode::$ValidateAppidError, null);
        return array(0, $xml_content);
    }
}
