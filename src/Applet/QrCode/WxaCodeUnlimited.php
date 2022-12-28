<?php

/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/12/27
 * Time: 2:55 下午
 */

declare(strict_types=1);

namespace PonyCool\Applet\QrCode;

use PonyCool\Applet\Utils\{Curl, Json};

class WxaCodeUnlimited
{

    /**
     * 获取小程序码，适用于需要的码数量极多的业务场景。通过该接口生成的小程序码，永久有效，数量暂无限制。
     * @param string $accessToken 接口调用凭证
     * @param string $scene
     * @param string|null $page 页面 page
     * @param bool $checkPath 检查page 是否存在
     * @param string $envVersion 要打开的小程序版本
     * @param int $width 二维码的宽度
     * @param bool $autoColor 自动配置线条颜色
     * @param array|null $lineColor
     * @param bool $isHyaline 是否需要透明底色
     * @return array
     */
    public static function action(string $accessToken, string $scene, ?string $page = null, bool $checkPath = true,
                                  string $envVersion = 'release', int $width = 430, bool $autoColor = false,
                                  ?array $lineColor = null, bool $isHyaline = false): array
    {
        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?";
        $params = [
            'access_token' => $accessToken,
        ];
        $data = [
            'scene' => $scene,
            'check_path' => $checkPath,
            'env_version' => $envVersion,
            'width' => $width,
            'auto_color' => $autoColor,
            'is_hyaline' => $isHyaline
        ];
        if (!is_null($page)) {
            $data['page'] = $page;
        }
        $env = [
            'release',
            'trial',
            'develop'
        ];
        if (!in_array($envVersion, $env, true)) {
            return [false, '无效的env_version'];
        }

        if ($width < 280 || $width > 1280) {
            return [false, '二维码的宽度超出范围，最小280px，最大1280px'];
        }

        if ($autoColor === false && !is_null($lineColor)) {
            $data['line_color'] = $lineColor;
        }

        $url .= http_build_query($params);

        $data = json_encode($data);

        $res = Curl::do($url, data: $data, method: 'POST');
        if (Json::isJsonStr($res)) {
            $res = json_decode($res, true);
        }
        if (is_array($res) && array_key_exists(strtolower("errCode"), $res)) {
            return [false, $res[strtolower("errMsg")]];
        }
        return [true, $res];
    }
}