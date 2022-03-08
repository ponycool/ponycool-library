<?php

/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/11/10
 * Time: 2:37 下午
 */
declare(strict_types=1);

namespace PonyCool\Lbs\Tencent\Coordinate;

use PonyCool\Lbs\Config;
use PonyCool\Lbs\Tencent\Request\Get;
use PonyCool\Lbs\Tencent\Result\Result;

class Translate
{
    /**
     * 坐标转换
     * @param Config $config
     * @param string $locations 预转换的坐标，支持批量转换
     * @param int $type 输入的locations的坐标类型
     * @param string $output 返回值类型
     * @param string|null $callback JSONP方式回调函数
     * @return array
     */
    public static function action(Config  $config, string $locations, int $type = 5, string $output = 'json',
                                  ?string $callback = null): array
    {
        $url = 'https://apis.map.qq.com/ws/coord/v1/translate?';
        $params = [
            'key' => $config->getKey(),
            'locations' => $locations,
            'type' => $type,
            'output' => $output,
        ];

        if (!is_null($callback)) {
            $params['callback'] = $callback;
        }
        $url .= http_build_query($params);
        $res = Get::exec($config, $url);
        return Result::data($res);
    }
}