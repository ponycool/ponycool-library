<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/11/10
 * Time: 10:21 上午
 */
declare(strict_types=1);

namespace PonyCool\Applet\SubscribeMessage;

use PonyCool\Applet\Utils\Curl;

class Message
{
    /**
     * 发送订阅消息
     * @param string $accessToken 令牌
     * @param string $toUser 接收者（用户）的 openid
     * @param string $templateId 所需下发的订阅模板id
     * @param array $data 模板内容，格式形如 { "key1": { "value": any }, "key2": { "value": any } }
     * @param string|null $page 点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数
     * @param string|null $state 跳转小程序类型：developer为开发版；trial为体验版；formal为正式版；默认为正式版
     * @param string|null $lang 进入小程序查看”的语言类型，支持zh_CN(简体中文)、en_US(英文)、zh_HK(繁体中文)、zh_TW(繁体中文)，默认为zh_CN
     * @return array
     */
    public static function send(string $accessToken, string $toUser, string $templateId, array $data, ?string $page,
                                ?string $state = 'formal',
                                ?string $lang = 'zh_CN'): array
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token=' . $accessToken;
        $params = [
            'touser' => $toUser,
            'template_id' => $templateId,
            'data' => $data,
            'miniprogram_state' => $state,
            'lang' => $lang
        ];
        if (!is_null($page)) {
            array_push($params, ['page' => $page]);
        }
        $params = json_encode($params);
        $res = Curl::do($url, $params, 'POST');
        $res = json_decode($res, true);
        if ($res['errcode'] !== 0) {
            return [false, $res];
        }
        return [true, $res];
    }
}