<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/9/5
 * Time: 4:17 下午
 */
declare(strict_types=1);

namespace PonyCool\Wechat\Menu;

use PonyCool\Wechat\Utils\Curl;

class CustomMenu
{
    /**
     * 获取自定义菜单
     * @param string $token
     * @return false|mixed
     */
    public static function get(string $token)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/menu/get?";
        $params = [
            'access_token' => $token,
        ];
        $url .= http_build_query($params);
        return Curl::get($url);
    }

    /**
     * 创建自定义菜单
     * @param string $token
     * @param string|array $menu
     * @return false|mixed
     */
    public static function create(string $token, $menu)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?";
        $params = [
            'access_token' => $token,
        ];
        $url .= http_build_query($params);
        return Curl::post($url, $menu);
    }
}
