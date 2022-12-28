<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/11/10
 * Time: 10:28 上午
 */
declare(strict_types=1);

namespace PonyCool\Applet\Token;

use PonyCool\Applet\Utils\Curl;

class AccessToken
{
    /**
     * 获取AccessToken
     * @param string $appId
     * @param string $secret
     * @return array
     */
    public static function get(string $appId, string $secret): array
    {
        $url = "https://api.weixin.qq.com/cgi-bin/token?";
        $params = [
            'grant_type' => 'client_credential',
            'appid' => $appId,
            'secret' => $secret
        ];
        $url .= http_build_query($params);
        $res = Curl::do($url);
        $res = json_decode($res, true);
        if (!array_key_exists('access_token', $res)) {
            return [false, $res];
        }
        return [true, $res];
    }
}
