<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2022/1/17
 * Time: 4:15 PM
 */
declare(strict_types=1);

namespace PonyCool\Applet\User;

use PonyCool\Applet\Utils\{Curl, Json};

class PhoneNumber
{
    /**
     * 获取用户手机号码
     * @param string $token 接口调用凭证
     * @param string $code 手机号获取凭证，BindGetPhoneNumber事件回调获取到动态令牌code，与wx.login返回的code作用是不一样的，不能混用
     * @return array
     */
    public static function get(string $token, string $code): array
    {
        $url = "https://api.weixin.qq.com/wxa/business/getuserphonenumber?";
        $params = [
            'access_token' => $token,
            'code' => $code
        ];
        $data = [
            'code' => $code
        ];

        $url .= http_build_query($params);

        $data = json_encode($data);

        log_message("debug", json_encode($params));
        $res = Curl::do($url, data: $data, method: 'POST');

        if (Json::isJsonStr($res)) {
            $res = json_decode($res, true);
        }
        if (is_array($res) && array_key_exists(strtolower("errCode"), $res)) {
            if ($res[strtolower("errCode")] === 0) {
                return [true, $res['phone_info']];
            }
            return [false, $res[strtolower("errMsg")] ?? '获取小程序用户手机号失败，返回结果不符合预期格式'];
        }
        return [false, $res];
    }
}