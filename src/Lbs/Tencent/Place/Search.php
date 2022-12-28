<?php

/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/11/6
 * Time: 4:57 下午
 */
declare(strict_types=1);

namespace PonyCool\Lbs\Tencent\Place;

use PonyCool\Lbs\Config;
use PonyCool\Lbs\Tencent\Request\Get;
use PonyCool\Lbs\Tencent\Result\Result;

class Search
{
    /**
     * 地点搜索
     * @param Config $config
     * @param string $keyword 搜索关键字，长度最大96个字节
     * @param string $boundary
     * @param string|null $filter 筛选条件
     * @param string|null $orderBy 排序
     * @param int|null $pageSize 每页条目数，最大限制为20条，默认为10条
     * @param int $pageIndex 第x页，默认第1页
     * @param string $output 返回格式：支持JSON/JSONP，默认JSON
     * @param string|null $callback JSONP方式回调函数
     * @return array
     */
    public static function action(Config  $config, string $keyword, string $boundary, ?string $filter = null,
                                  ?string $orderBy = null, ?int $pageSize = 10, int $pageIndex = 1,
                                  string  $output = 'json', ?string $callback = null): array
    {
        $url = 'https://apis.map.qq.com/ws/place/v1/search?';
        $params = [
            'key' => $config->getKey(),
            'keyword' => $keyword,
            'boundary' => $boundary,
            'page_size' => $pageSize,
            'page_Index' => $pageIndex,
            'output' => $output,
        ];
        if (!is_null($filter)) {
            $params['filter'] = $filter;
        }
        if (!is_null($orderBy)) {
            $params[strtolower('orderBy')] = $orderBy;
        }
        if (!is_null($callback)) {
            $params['callback'] = $callback;
        }
        $url .= http_build_query($params);
        $res = Get::exec($config, $url);
        return Result::data($res);
    }
}