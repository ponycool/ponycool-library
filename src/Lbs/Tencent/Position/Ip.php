<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/6/14
 * Time: 23:10
 */
declare(strict_types=1);

namespace PonyCool\Lbs\Tencent\Position;

use PonyCool\Lbs\Config;
use PonyCool\Lbs\Tencent\Request\Get;
use PonyCool\Lbs\Tencent\Result\Result;

class Ip
{
    /**
     * 获取IP定位
     */
    public static function getPosition(Config $config, string $ip): array
    {
        $url = 'https://apis.map.qq.com/ws/location/v1/ip?';
        $params = [
            'key' => $config->getKey(),
            'ip' => $ip,
        ];
        $url .= http_build_query($params);
        $res = Get::exec($config, $url);
        return Result::data($res);
    }
}