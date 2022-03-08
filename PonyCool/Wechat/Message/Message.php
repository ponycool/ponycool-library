<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/9/2
 * Time: 2:59 下午
 */
declare(strict_types=1);

namespace PonyCool\Wechat\Message;

use PonyCool\Wechat\Signature\Signature;
use PonyCool\Wechat\Utils\{AesCrypt, ErrorCode, XMLParse};
use Exception;

class Message
{
    /**
     * 获取被动消息内容
     * @param string $mode 消息加解密方式：明文模式plain、兼容模式compatible、安全模式safe
     * @param string $appId
     * @param string $encodingAESKey
     * @param string $token
     * @return array
     */
    public static function getContent(string $mode = 'safe', string $appId = '', string $encodingAESKey = '', string $token = ''): array
    {
        $postData = file_get_contents('php://input');
        try {
            if (empty($postData)) {
                throw new Exception('消息体为空', ErrorCode::$ParseXmlError);
            }
            switch ($mode) {
                case 'plain':
                case 'compatible':
                    $parseRes = XMLParse::extract($postData);
                    break;
                case 'safe':
                    $parseRes = XMLParse::extractEncryptMessage($postData);
                    break;
                default:
                    throw new Exception('无效的消息加解密方式，合法的加密方式为:plain、compatible、safe');
            }
            if ($parseRes[0] !== 0) {
                throw new Exception('XML解析失败', ErrorCode::$ParseXmlError);
            }
            $msgContent = $parseRes[1];
            // 安全模式解密消息
            if ($mode === 'safe') {
                // 校验encodingAESKey
                if (strlen($encodingAESKey) !== 43) {
                    throw new Exception('EncodingAESKey无效，消息加密密钥为43位字符');
                }
                // 校验token
                if (strlen($token) === 0) {
                    throw new Exception('wechat token无效');
                }
                // 校验签名
                if (!Signature::verify($token, $msgContent)) {
                    throw new Exception('消息加密签名包验签失败');
                }
                // 校验AppID
                if (strlen($appId) === 0) {
                    throw new Exception('appId无效');
                }
                // 解密消息
                $aesCrypt = new AesCrypt($encodingAESKey);
                $decryptRes = $aesCrypt->decrypt($msgContent, $appId);
                if ($decryptRes[0] !== 0) {
                    throw new Exception('消息解密失败');
                }
                $xmlContent = $decryptRes[1];
                $content = XMLParse::extract($xmlContent);
                $res = $content[0];
                if ($res !== 0) {
                    throw new Exception('加密xml解析失败');
                }
                $msgContent = $content[1];
            }
            return [0, $msgContent];
        } catch (Exception $e) {
            return [ErrorCode::$ParseMessage, '获取微信被动消息失败，error:' . $e->getMessage()];
        }
    }
}
