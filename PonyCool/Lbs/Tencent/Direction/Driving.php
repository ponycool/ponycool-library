<?php

/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/11/8
 * Time: 10:39 上午
 */
declare(strict_types=1);

namespace PonyCool\Lbs\Tencent\Direction;

use PonyCool\Lbs\Config;
use PonyCool\Lbs\Tencent\Request\Get;
use PonyCool\Lbs\Tencent\Result\Result;

class Driving
{
    /**
     * 驾车路线规划
     * @param Config $config
     * @param string $from 起点位置坐标
     * @param string $to 终点位置坐标
     * @param string|null $fromPoi 起点POI ID，传入后，优先级高于from（坐标）
     * @param int|null $heading 在起点位置时的车头方向，数值型，取值范围0至360（0度代表正北，顺时针一周360度）
     * @param int|null $speed 速度，单位：米/秒，默认3。 当速度低于1.39米/秒时，heading将被忽略
     * @param int $accuracy 定位精度，单位：米，取>0数值，默认5。
     * @param int $roadType 起点道路类型
     * @param string|null $fromTrack 起点轨迹
     * @param string|null $toPoi 终点POI ID
     * @param string|null $waypoints 途经点
     * @param string|null $policy 策略参数
     * @param string|null $avoidPolygons 避让区域：支持32个避让区域，每个区域最多可有9个顶点
     * @param string|null $plateNumber 车牌号，填入后，路线引擎会根据车牌对限行区域进行避让，不填则不不考虑限行问题
     * @param int $carType 车辆类型
     * @param int $getMp 是否返回多方案
     * @param int $noStep 不返回路线引导信息，可使回包数据量更小
     * @param string|null $output 返回值类型
     * @param string|null $callback 回调函数
     * @return array
     */
    public static function action(Config  $config, string $from, string $to, ?string $fromPoi = null, ?int $heading = null,
                                  ?int    $speed = 3, int $accuracy = 5, int $roadType = 0, ?string $fromTrack = null,
                                  ?string $toPoi = null, ?string $waypoints = null, ?string $policy = null,
                                  ?string $avoidPolygons = null, ?string $plateNumber = null, int $carType = 0,
                                  int     $getMp = 0, int $noStep = 0, ?string $output = 'json', ?string $callback = null
    ): array
    {
        $url = 'https://apis.map.qq.com/ws/direction/v1/driving/?';
        $params = [
            'key' => $config->getKey(),
            'from' => $from,
            'to' => $to,
            'speed' => $speed,
            'accuracy' => $accuracy,
            'road_type' => $roadType,
            'cartype' => $carType,
            'get_mp' => $getMp,
            'no_step' => $noStep,
            'output' => $output,
        ];
        if (!is_null($fromPoi)) {
            $params['from_poi'] = $fromPoi;
        }
        if (!is_null($heading)) {
            $params['heading'] = $heading;
        }
        if (!is_null($fromTrack)) {
            $params['from_track'] = $fromTrack;
        }
        if (!is_null($toPoi)) {
            $params['to_poi'] = $toPoi;
        }
        if (!is_null($waypoints)) {
            $params['waypoints'] = $waypoints;
        }
        if (!is_null($policy)) {
            $params['policy'] = $policy;
        }
        if (!is_null($avoidPolygons)) {
            $params['avoid_polygons'] = $avoidPolygons;
        }
        if (!is_null($plateNumber)) {
            $params['plate_number'] = $plateNumber;
        }
        if (!is_null($callback)) {
            $params['callback'] = $callback;
        }
        $url .= http_build_query($params);
        $res = Get::exec($config, $url);
        return Result::data($res);
    }
}