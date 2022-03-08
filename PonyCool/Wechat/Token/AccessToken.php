<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/9/5
 * Time: 11:22 上午
 */
declare(strict_types=1);

namespace PonyCool\Wechat\Token;

use PonyCool\Wechat\Utils\Curl;

class AccessToken
{
    /**
     * 获取AccessToken
     * @param string $appId 开发者ID
     * @param string $secret 开发者密钥
     * @return false|mixed
     */
    public static function get(string $appId, string $secret)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&";
        $params = [
            'appid' => $appId,
            'secret' => $secret
        ];
        $url .= http_build_query($params);
        $res = Curl::get($url);
        $res = json_decode($res, true);
        if (!array_key_exists('access_token', $res)) {
            return false;
        }
        return $res;
    }
}
