<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/11/8
 * Time: 9:44 上午
 */

declare(strict_types=1);

namespace PonyCool\Lbs\Tencent\Place;

use PonyCool\Lbs\Config;
use PonyCool\Lbs\Tencent\Request\Get;
use PonyCool\Lbs\Tencent\Result\Result;

class Suggestion
{
    /**
     * 关键词输入提示
     * @param Config $config
     * @param string $keyword 用户输入的关键词
     * @param string|null $region 限制城市范围
     * @param int $regionFix
     * @param string|null $location 定位坐标
     * @param int $getSubPoi 是否返回子地点
     * @param int $policy 检索策略
     * @param string|null $filter 筛选条件
     * @param string|null $addressFormat
     * @param int|null $pageSize 每页条数
     * @param int $pageIndex 页码
     * @param string $output 返回格式：支持JSON/JSONP，默认JSON
     * @param string|null $callback JSONP方式回调函数
     * @return array
     */
    public static function action(Config  $config, string $keyword, ?string $region = null, int $regionFix = 0,
                                  ?string $location = null, int $getSubPoi = 0, int $policy = 0, ?string $filter = null,
                                  ?string $addressFormat = null, ?int $pageSize = 10, int $pageIndex = 1,
                                  string  $output = 'json', ?string $callback = null): array
    {
        $url = 'https://apis.map.qq.com/ws/place/v1/suggestion?';
        $params = [
            'key' => $config->getKey(),
            'keyword' => $keyword,
            'region_fix' => $regionFix,
            'get_subpois' => $getSubPoi,
            'policy' => $policy,
            'page_size' => $pageSize,
            'page_Index' => $pageIndex,
            'output' => $output,
        ];
        if (!is_null($region)) {
            $params['region'] = $region;
        }
        if (!is_null($location)) {
            $params['location'] = $location;
        }
        if (!is_null($filter)) {
            $params['filter'] = $filter;
        }
        if (!is_null($addressFormat)) {
            $params['address_format'] = $addressFormat;
        }
        if (!is_null($callback)) {
            $params['callback'] = $callback;
        }
        $url .= http_build_query($params);
        $res = Get::exec($config, $url);
        return Result::data($res);
    }
}