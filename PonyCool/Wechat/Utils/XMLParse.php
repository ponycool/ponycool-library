<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/9/2
 * Time: 2:30 下午
 */
declare(strict_types=1);

namespace PonyCool\Wechat\Utils;

use DOMDocument;
use Exception;

/**
 * 提供提取消息格式中的密文及生成回复消息格式的接口.
 * Class XMLParse
 * @package PonyCool\Wechat\Utils
 */
class XMLParse
{
    /**
     * 提取出xml数据包中的消息
     * @param string $xmlText 待提取的xml字符串
     * @return array 提取出的消息字符串
     */
    public static function extract(string $xmlText): array
    {
        try {
            $xml = new DOMDocument();
            $xml->loadXML($xmlText);
            $elems = $xml->documentElement;
            $message = [];
            foreach ($elems->childNodes as $item) {
                $message[$item->nodeName] = $item->nodeValue;
            }
            unset($message['#text']);
            $array_a = $xml->getElementsByTagName('ToUserName');
            $toUserName = $array_a->item(0)->nodeValue;
            return [0, $message, $toUserName];
        } catch (Exception $e) {
            return [ErrorCode::$ParseXmlError, null, null];
        }
    }

    /**
     * 提取出xml数据包中的加密消息
     * @param string $xmlText 待提取的xml字符串
     * @return array 提取出的加密消息字符串
     */
    public static function extractEncryptMessage(string $xmlText): array
    {
        try {
            $xml = new DOMDocument();
            $xml->loadXML($xmlText);
            $array_e = $xml->getElementsByTagName('Encrypt');
            $array_a = $xml->getElementsByTagName('ToUserName');
            $encrypt = $array_e->item(0)->nodeValue;
            $toUserName = $array_a->item(0)->nodeValue;
            return [0, $encrypt, $toUserName];
        } catch (Exception $e) {
            return [ErrorCode::$ParseXmlError, null, null];
        }
    }

    /**
     * 生成xml消息
     * @param string $encrypt 加密后的消息密文
     * @param string $signature 安全签名
     * @param string $timestamp 时间戳
     * @param string $nonce 随机字符串
     * @return string
     */
    public static function generate(string $encrypt, string $signature, string $timestamp, string $nonce): string
    {
        $format = "<xml>";
        $format .= "<Encrypt><![CDATA[%s]]></Encrypt>";
        $format .= "<MsgSignature><![CDATA[%s]]></MsgSignature>";
        $format .= "<TimeStamp>%s</TimeStamp>";
        $format .= "<Nonce><![CDATA[%s]]></Nonce>";
        $format .= "</xml>";
        return sprintf($format, $encrypt, $signature, $timestamp, $nonce);
    }
}
