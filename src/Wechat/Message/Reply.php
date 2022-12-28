<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/9/3
 * Time: 3:45 下午
 */
declare(strict_types=1);

namespace PonyCool\Wechat\Message;

class Reply
{
    /**
     * 回复文本消息
     * @param object $res 消息请求
     * @param string $msg 回复消息内容
     * @param string $timestamp
     * @return string
     */
    public static function text(object $res, string $msg, string $timestamp): string
    {
        $format = "<xml>";
        $format .= "<ToUserName><![CDATA[%s]]></ToUserName>";
        $format .= "<FromUserName><![CDATA[%s]]></FromUserName>";
        $format .= "<CreateTime>%s</CreateTime> ";
        $format .= "<MsgType><![CDATA[text]]></MsgType>";
        $format .= "<Content><![CDATA[%s]]></Content>";
        $format .= "</xml>";
        return sprintf($format, $res->FromUserName, $res->ToUserName, $timestamp, $msg);
    }


    /**
     * 回复图片消息
     * @param object $res 消息请求
     * @param string $mediaId 通过素材管理中的接口上传多媒体文件，得到的id。
     * @param string $timestamp
     * @return string
     */
    public static function image(object $res, string $mediaId, string $timestamp): string
    {
        $format = "<xml>";
        $format .= "<ToUserName><![CDATA[%s]]></ToUserName>";
        $format .= "<FromUserName><![CDATA[%s]]></FromUserName>";
        $format .= "<CreateTime>%s</CreateTime> ";
        $format .= "<MsgType><![CDATA[image]]></MsgType>";
        $format .= "<Image><MediaId><![CDATA[%s]]></MediaId></Image>";
        $format .= "</xml>";
        return sprintf($format, $res->FromUserName, $res->ToUserName, $timestamp, $mediaId);
    }

    /**
     * 回复加密消息
     * @param string $encrypt 加密后的消息密文
     * @param string $signature 安全签名
     * @param string $timestamp 时间戳
     * @param string $nonce 随机字符串
     * @return string
     */
    public static function cryptMsg(string $encrypt, string $signature, string $timestamp, string $nonce): string
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
