<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/11/8
 * Time: 11:11 上午
 */
declare(strict_types=1);

namespace PonyCool\Lbs\Tencent\Direction;

use PonyCool\Lbs\Config;
use PonyCool\Lbs\Tencent\Request\Get;
use PonyCool\Lbs\Tencent\Result\Result;

class Walking
{
    /**
     * 步行路线规划
     * @param Config $config
     * @param string $from 起点位置坐标
     * @param string $to 终点位置坐标
     * @param string|null $toPoi 终点POI ID
     * @param string|null $output 返回值类型
     * @param string|null $callback 回调函数
     * @return array
     */
    public static function action(Config  $config, string $from, string $to, ?string $toPoi = null,
                                  ?string $output = 'json', ?string $callback = null): array
    {
        $url = 'https://apis.map.qq.com/ws/direction/v1/walking/?';
        $params = [
            'key' => $config->getKey(),
            'from' => $from,
            'to' => $to,
            'output' => $output,
        ];

        if (!is_null($toPoi)) {
            $params['to_poi'] = $toPoi;
        }
        if (!is_null($callback)) {
            $params['callback'] = $callback;
        }
        $url .= http_build_query($params);
        $res = Get::exec($config, $url);
        return Result::data($res);
    }
}