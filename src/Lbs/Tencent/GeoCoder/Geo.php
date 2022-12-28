<?php

/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/11/5
 * Time: 2:43 下午
 */

declare(strict_types=1);

namespace PonyCool\Lbs\Tencent\GeoCoder;

use PonyCool\Lbs\Config;
use PonyCool\Lbs\Tencent\Request\Get;
use PonyCool\Lbs\Tencent\Result\Result;

class Geo
{
    /**
     * 逆地址解析
     * 本接口提供由经纬度到文字地址及相关位置信息的转换能力
     * @param Config $config
     * @param string $location 经纬度（GCJ02坐标系），格式：location=lat<纬度>,lng<经度>
     * @param int|null $getPoi 是否返回周边地点（POI）列表
     * @param string|null $poiOptions 周边POI列表控制参数
     * @param string|null $output 返回格式：支持JSON/JSONP，默认JSON
     * @param string|null $callback JSONP方式回调函数
     * @return array
     */
    public static function locationToAddress(Config  $config, string $location, ?int $getPoi = 0, ?string $poiOptions = null,
                                             ?string $output = 'json', ?string $callback = null): array
    {
        $url = 'https://apis.map.qq.com/ws/geocoder/v1/?';
        $params = [
            'key' => $config->getKey(),
            'location' => $location,
            'get_poi' => $getPoi,
            'output' => $output,
        ];
        if (!is_null($poiOptions)) {
            $params['poi_options'] = $poiOptions;
        }
        if (!is_null($callback)) {
            $params['callback'] = $callback;
        }
        $url .= http_build_query($params);
        $res = Get::exec($config, $url);
        return Result::data($res);
    }

    /**
     * 地址解析
     * @param Config $config
     * @param string $address 地址（注：地址中请包含城市名称，否则会影响解析效果）
     * @param string|null $region 地址所在城市（若地址中包含城市名称侧可不传）
     * @param string|null $output 返回格式：支持JSON/JSONP，默认JSON
     * @param string|null $callback JSONP方式回调函数
     * @return array
     */
    public static function addressToLocation(Config  $config, string $address, ?string $region = null, ?string $output = 'json',
                                             ?string $callback = null): array
    {
        $url = 'https://apis.map.qq.com/ws/geocoder/v1/?';
        $params = [
            'key' => $config->getKey(),
            'address' => $address,
            'output' => $output,
        ];
        if (!is_null($region)) {
            $params['region'] = $region;
        }
        if (!is_null($callback)) {
            $params['callback'] = $callback;
        }
        $url .= http_build_query($params);
        $res = Get::exec($config, $url);
        return Result::data($res);
    }

    /**
     * 智能地址解析
     * @param Config $config
     * @param string $address 地址
     * @param string|null $region 地址所在城市
     * @param string|null $output 返回格式：支持JSON/JSONP，默认JSON
     * @param string|null $callback JSONP方式回调函数
     * @return array
     */
    public static function smartAddress(Config  $config, string $address, ?string $region = null, ?string $output = 'json',
                                        ?string $callback = null): array
    {
        $url = 'https://apis.map.qq.com/ws/geocoder/v1/?';
        $params = [
            'key' => $config->getKey(),
            'smart_address' => $address,
            'output' => $output,
        ];
        if (!is_null($region)) {
            $params['region'] = $region;
        }
        if (!is_null($callback)) {
            $params['callback'] = $callback;
        }
        $url .= http_build_query($params);
        $res = Get::exec($config, $url);
        return Result::data($res);
    }
}