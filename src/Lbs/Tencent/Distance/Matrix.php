<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/11/8
 * Time: 2:28 下午
 */
declare(strict_types=1);

namespace PonyCool\Lbs\Tencent\Distance;

use PonyCool\Lbs\Config;
use PonyCool\Lbs\Tencent\Request\Get;
use PonyCool\Lbs\Tencent\Result\Result;

class Matrix
{
    /**
     * 批量矩阵计算
     * @param Config $config
     * @param string $from 起点位置坐标
     * @param string $to 终点位置坐标
     * @param string $mode 计算方式
     * @param string|null $output 返回值类型
     * @param string|null $callback 回调函数
     * @return array
     */
    public static function action(Config  $config, string $from, string $to, string $mode = 'driving',
                                  ?string $output = 'json', ?string $callback = null): array
    {
        $url = 'https://apis.map.qq.com/ws/distance/v1/matrix/?';
        $params = [
            'key' => $config->getKey(),
            'mode' => $mode,
            'from' => $from,
            'to' => $to,
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