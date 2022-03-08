<?php

/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/11/8
 * Time: 2:48 下午
 */
declare(strict_types=1);

namespace PonyCool\Lbs\Tencent\TrafficStatus;

use PonyCool\Lbs\Config;
use PonyCool\Lbs\Tencent\Request\Post;
use PonyCool\Lbs\Tencent\Result\Result;

class Search
{
    /**
     * 路况查询
     * @param Config $config
     * @param array $track 查询路况的对应轨迹点串
     * @param int $mode 路况模式
     * @param int|null $timestamp 查询指定时间下的路况 格式：Unix时间戳，支持3个月内任意时间
     * @return array
     */
    public static function action(Config $config, array $track, int $mode = 0, ?int $timestamp = null): array
    {
        $url = 'https://apis.map.qq.com/ws/traffic_status';
        $params = [
            'mode' => $mode,
            'key' => $config->getKey(),
            'track' => $track,
        ];

        if (!is_null($timestamp)) {
            $params['timestamp'] = $timestamp;
        }
        $res = Post::exec($config, $url, $params);
        return Result::data($res);
    }
}