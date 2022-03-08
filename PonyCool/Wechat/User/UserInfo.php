<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/5/11
 * Time: 10:07 上午
 */
declare(strict_types=1);

namespace PonyCool\Wechat\User;

use PonyCool\Wechat\Utils\Curl;

class UserInfo
{
    public function get()
    {
        // todo 获取用户信息
    }

    /**
     * 通过网页授权获取用户信息
     * @param string $token
     * @param string $openID
     * @param string $lang
     * @return false|mixed
     */
    public static function getWithSnsApi(string $token, string $openID, string $lang = 'zh_CN')
    {
        $url = "https://api.weixin.qq.com/sns/userinfo?";
        $params = [
            'access_token' => $token,
            'openid' => $openID,
            'lang' => $lang
        ];
        $url .= http_build_query($params);
        return Curl::get($url);
    }
}