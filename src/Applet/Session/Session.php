<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/9/8
 * Time: 10:23 下午
 */
declare(strict_types=1);

namespace PonyCool\Applet\Session;

use PonyCool\Applet\Utils\Curl;

class Session
{
    /**
     * 获取小程序Session
     * @param string $appId 小程序 appId
     * @param string $secret 小程序 appSecret
     * @param string $code 登录时获取的 code
     * @return array
     */
    public static function get(string $appId, string $secret, string $code): array
    {
        $url = 'https://api.weixin.qq.com/sns/jscode2session?';
        $params = [
            'appid' => $appId,
            'secret' => $secret,
            'js_code' => $code,
            'grant_type' => 'authorization_code'
        ];
        $url .= http_build_query($params);
        $res = Curl::do($url);
        if ($res === false) {
            return [false, '请求未正确响应'];
        }
        $res = json_decode($res, true);
        if (array_key_exists('errcode', $res)) {
            return [false, $res];
        }
        return [true, $res];
    }
}
