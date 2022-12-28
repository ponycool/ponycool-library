<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/11/8
 * Time: 11:25 上午
 */
declare(strict_types=1);

namespace PonyCool\Lbs\Tencent\Direction;

use PonyCool\Lbs\Config;
use PonyCool\Lbs\Tencent\Request\Get;
use PonyCool\Lbs\Tencent\Result\Result;

class Transit
{
    /**
     * 公交路线规划
     * @param Config $config
     * @param string $from 起点位置坐标
     * @param string $to 终点位置坐标
     * @param int|null $departureTime 出发时间，用于过滤掉非运营时段的线路，格式为Unix时间戳，默认使用当前时间
     * @param string|null $policy 路线计算偏好
     * @param string|null $output 返回值类型
     * @param string|null $callback 回调函数
     * @return array
     */
    public static function action(Config  $config, string $from, string $to, ?int $departureTime = null, ?string $policy = null,
                                  ?string $output = 'json', ?string $callback = null): array
    {
        $url = 'https://apis.map.qq.com/ws/direction/v1/transit/?';
        $params = [
            'key' => $config->getKey(),
            'from' => $from,
            'to' => $to,
            'output' => $output,
        ];

        if (!is_null($departureTime)) {
            $params['departure_time'] = $departureTime;
        }
        if (!is_null($policy)) {
            $params['policy'] = $policy;
        }
        if (!is_null($callback)) {
            $params['callback'] = $callback;
        }
        $url .= http_build_query($params);
        $res = Get::exec($config, $url);
        return Result::data($res);
    }
}