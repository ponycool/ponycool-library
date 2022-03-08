<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/11/10
 * Time: 3:16 下午
 */
declare(strict_types=1);

namespace PonyCool\Lbs\Tencent\District;

use PonyCool\Lbs\Config;
use PonyCool\Lbs\Tencent\Request\Get;
use PonyCool\Lbs\Tencent\Result\Result;

class District
{
    /**
     * 获取省市区列表
     * @param Config $config
     * @param string $output 返回格式：支持JSON/JSONP，默认JSON
     * @param string|null $callback JSONP方式回调函数
     * @return array
     */
    public static function list(Config $config, string $output = 'json', ?string $callback = null): array
    {
        $url = 'https://apis.map.qq.com/ws/district/v1/list?';
        $params = [
            'key' => $config->getKey(),
            'output' => $output,
        ];

        if (!is_null($callback)) {
            $params['callback'] = $callback;
        }
        $url .= http_build_query($params);
        $res = Get::exec($config, $url);
        return Result::data($res);
    }

    /**
     * 获取下级行政区划
     * @param Config $config
     * @param int|null $id 父级行政区划ID
     * @param int|null $getPolygon 返回行政区划轮廓点串
     * @param int|null $maxOffset 轮廓点串的抽稀精度
     * @param string $output 返回格式：支持JSON/JSONP，默认JSON
     * @param string|null $callback JSONP方式回调函数
     * @return array
     */
    public static function getChildren(Config $config, ?int $id = null, ?int $getPolygon = 0, ?int $maxOffset = null,
                                       string $output = 'json', ?string $callback = null): array
    {
        $url = 'https://apis.map.qq.com/ws/district/v1/getchildren?';
        $params = [
            'key' => $config->getKey(),
            'output' => $output,
        ];

        if (!is_null($id)) {
            $params['id'] = $id;
        }
        if (!is_null($getPolygon)) {
            $params['get_polygon'] = $getPolygon;
        }
        if (!is_null($maxOffset)) {
            $params['max_offset'] = $maxOffset;
        }
        if (!is_null($callback)) {
            $params['callback'] = $callback;
        }
        $url .= http_build_query($params);
        $res = Get::exec($config, $url);
        return Result::data($res);
    }

    /**
     * 行政区划搜索
     * @param Config $config
     * @param string $keyword 搜索关键词
     * @param int|null $getPolygon 返回行政区划轮廓点串
     * @param int|null $maxOffset 轮廓点串的抽稀精度
     * @param string $output 返回格式：支持JSON/JSONP，默认JSON
     * @param string|null $callback JSONP方式回调函数
     * @return array
     */
    public static function search(Config $config, string $keyword, ?int $getPolygon = 0, ?int $maxOffset = null,
                                  string $output = 'json', ?string $callback = null): array
    {
        $url = 'https://apis.map.qq.com/ws/district/v1/search?';
        $params = [
            'key' => $config->getKey(),
            'keyword' => $keyword,
            'output' => $output,
        ];

        if (!is_null($maxOffset)) {
            $params['max_offset'] = $maxOffset;
        }
        if (!is_null($getPolygon)) {
            $params['get_polygon'] = $getPolygon;
        }
        if (!is_null($callback)) {
            $params['callback'] = $callback;
        }
        $url .= http_build_query($params);
        $res = Get::exec($config, $url);
        return Result::data($res);
    }
}