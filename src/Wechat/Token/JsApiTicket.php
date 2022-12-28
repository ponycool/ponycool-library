<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/9/5
 * Time: 11:51 上午
 */
declare(strict_types=1);

namespace PonyCool\Wechat\Token;


use PonyCool\Wechat\Utils\Curl;

class JsApiTicket
{
    /**
     * 获取微信JS接口的临时票据
     * @param string $token
     * @return false|mixed
     */
    public static function getTicket(string $token)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?";
        $params = array(
            'access_token' => $token,
            'type' => 'jsapi'
        );
        $url .= http_build_query($params);
        $res = Curl::get($url);
        $res = json_decode($res, true);
        if (!array_key_exists('ticket', $res)) {
            return false;
        }
        return $res;
    }
}
