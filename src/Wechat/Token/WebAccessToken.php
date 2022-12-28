<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/9/5
 * Time: 11:48 上午
 */
declare(strict_types=1);

namespace PonyCool\Wechat\Token;

use PonyCool\Wechat\Utils\Curl;

class WebAccessToken
{
    /**
     * 获取网页授权 AccessToken
     * @param string $appId 开发者ID
     * @param string $secret 开发者密钥
     * @param string $code
     * @return false|mixed
     */
    public static function get(string $appId, string $secret, string $code)
    {
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?";
        $params = array(
            'appid' => $appId,
            'secret' => $secret,
            'code' => $code,
            'grant_type' => 'authorization_code'
        );
        $url .= http_build_query($params);
        $res = Curl::get($url);
        $res = json_decode($res, true);
        if (!array_key_exists('access_token', $res)) {
            return false;
        }
        return $res;
    }

    /**
     * 刷新网页AccessToken
     * @param string $appId 开发者ID
     * @param string $refreshToken 刷新令牌
     * @return false|mixed
     */
    public static function refresh(string $appId, string $refreshToken)
    {
        $url = "https://api.weixin.qq.com/sns/oauth2/refresh_token?";
        $params = array(
            'appid' => $appId,
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken
        );
        $url .= http_build_query($params);
        $res = Curl::get($url);
        $res = json_decode($res, true);
        if (!array_key_exists('access_token', $res)) {
            return false;
        }
        return $res;
    }
}
