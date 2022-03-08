<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/6/14
 * Time: 22:52
 */
declare(strict_types=1);

namespace PonyCool\Lbs\Tencent;


use PonyCool\Lbs\Config;
use PonyCool\Lbs\LbsInterface;
use PonyCool\Lbs\Tencent\Direction\{Bicycling, Driving, Transit, Walking};
use PonyCool\Lbs\Tencent\Coordinate\Translate;
use PonyCool\Lbs\Tencent\Distance\Matrix;
use PonyCool\Lbs\Tencent\District\District;
use PonyCool\Lbs\Tencent\GeoCoder\Geo;
use PonyCool\Lbs\Tencent\Place\{Search, Suggestion};
use PonyCool\Lbs\Tencent\TrafficStatus\Search as TrafficStatus;
use PonyCool\Lbs\Tencent\Position\Ip;
use PonyCool\Lbs\Tencent\Signature\Sign;

class Lbs implements LbsInterface
{

    private Config $config;

    public function checkConfig(Config $config): bool
    {
        if (empty($config->getSource())) {
            return false;
        }
        if (empty($config->getKey())) {
            return false;
        }
        // 校验签名配置
        if ($config->isSign() === true && is_null($config->getSecretKey())) {
            return false;
        }
        $this->config = $config;
        return true;
    }

    /**
     * 签名
     * @param string $url
     * @param array|null $data
     * @return string
     */
    public function sign(string $url, ?array $data = null): string
    {
        return Sign::get($this->config->getSecretKey(), $url, $data);
    }

    /**
     * 获取IP定位
     * @param string $ip
     * @return array
     */
    public function ip(string $ip): array
    {
        return Ip::getPosition($this->config, $ip);
    }

    /**
     * 逆地址解析
     * 本接口提供由经纬度到文字地址及相关位置信息的转换能力
     * @param string $location 经纬度（GCJ02坐标系），格式：location=lat<纬度>,lng<经度>
     * @param string|null $output 返回格式：支持JSON/JSONP，默认JSON
     * @return array
     */
    public function locationToAddress(string $location, ?string $output = 'json'): array
    {
        return Geo::locationToAddress($this->config, $location, output: $output);
    }

    /**
     * 地址解析
     * @param string $address 地址（注：地址中请包含城市名称，否则会影响解析效果）
     * @param string|null $region 地址所在城市（若地址中包含城市名称侧可不传）
     * @param string|null $output 返回格式：支持JSON/JSONP，默认JSON
     * @return array
     */
    public function addressToLocation(string $address, ?string $region = null, ?string $output = 'json'): array
    {
        return Geo::addressToLocation($this->config, $address, $region, $output);
    }

    /**
     * 智能地址解析
     * @param string $address 地址
     * @param string|null $region 地址所在城市
     * @param string|null $output 返回格式：支持JSON/JSONP，默认JSON
     * @return array
     */
    public function smartAddress(string $address, ?string $region = null, ?string $output = 'json'): array
    {
        return Geo::smartAddress($this->config, $address, $region, $output);
    }

    /**
     * 地点搜索
     * @param string $keyword 搜索关键字，长度最大96个字节
     * @param string $boundary
     * @param string|null $output 返回格式：支持JSON/JSONP，默认JSON
     * @return array
     */
    public function placeSearch(string $keyword, string $boundary, ?string $output = 'json'): array
    {
        return Search::action($this->config, $keyword, $boundary, output: $output);
    }

    /**
     * 关键词输入提示
     * @param string $keyword 用户输入的关键词
     * @param string|null $region 限制城市范围
     * @param string|null $output 返回格式：支持JSON/JSONP，默认JSON
     * @return array
     */
    public function placeSuggestion(string $keyword, ?string $region = null, ?string $output = 'json'): array
    {
        return Suggestion::action($this->config, $keyword, $region, output: $output);
    }

    /**
     * 驾车路线规划
     * @param string $from 起点位置坐标
     * @param string $to 终点位置坐标
     * @param string|null $output 返回值类型
     * @return array
     */
    public function drivingDirection(string $from, string $to, ?string $output = 'json'): array
    {
        return Driving::action($this->config, $from, $to, output: $output);
    }

    /**
     * 步行路线规划
     * @param string $from 起点位置坐标
     * @param string $to 终点位置坐标
     * @param string|null $output 返回值类型
     * @return array
     */
    public function walkingDirection(string $from, string $to, ?string $output = 'json'): array
    {
        return Walking::action($this->config, $from, $to, output: $output);
    }

    /**
     * 骑行路线规划
     * @param string $from 起点位置坐标
     * @param string $to 终点位置坐标
     * @param string|null $output 返回值类型
     * @return array
     */
    public function bicyclingDirection(string $from, string $to, ?string $output = 'json'): array
    {
        return Bicycling::action($this->config, $from, $to, output: $output);
    }

    /**
     * 公交路线规划
     * @param string $from 起点位置坐标
     * @param string $to 终点位置坐标
     * @param string|null $output 返回值类型
     * @return array
     */
    public function transitDirection(string $from, string $to, ?string $output = 'json'): array
    {
        return Transit::action($this->config, $from, $to, output: $output);
    }

    /**
     * 批量矩阵计算
     * @param string $from 起点位置坐标
     * @param string $to 终点位置坐标
     * @param string $mode 计算方式
     * @param string|null $output 返回值类型
     * @return array
     */
    public function matrixDistance(string $from, string $to, string $mode = 'driving', ?string $output = 'json'): array
    {
        return Matrix::action($this->config, $from, $to, $mode, output: $output);
    }

    /**
     * 路况查询
     * @param array $track 查询路况的对应轨迹点串
     * @param int $mode 路况模式
     * @param int|null $timestamp 查询指定时间下的路况 格式：Unix时间戳，支持3个月内任意时间
     * @return array
     */
    public function trafficStatusSearch(array $track, int $mode = 0, ?int $timestamp = null): array
    {
        return TrafficStatus::action($this->config, $track, $mode, $timestamp);
    }

    /**
     * @param string $locations 预转换的坐标，支持批量转换
     * @param int $type 输入的locations的坐标类型
     * @param string|null $output 返回值类型
     * @return array
     */
    public function coordinateTranslate(string $locations, int $type = 5, ?string $output = 'json'): array
    {
        return Translate::action($this->config, $locations, $type, output: $output);
    }


    /**
     * 获取省市区列表
     * @param string|null $output 返回格式：支持JSON/JSONP，默认JSON
     * @return array
     */
    public function getDistrictList(?string $output = 'json'): array
    {
        return District::list($this->config, output: $output);
    }

    /**
     * 获取下级行政区划
     * @param int|null $id 父级行政区划ID
     * @param int|null $getPolygon 返回行政区划轮廓点串
     * @param int|null $maxOffset 轮廓点串的抽稀精度
     * @param string|null $output 返回格式：支持JSON/JSONP，默认JSON
     * @return array
     */
    public function getDistrictChildren(?int    $id = null, ?int $getPolygon = 0, ?int $maxOffset = null,
                                        ?string $output = 'json'): array
    {
        return District::getChildren($this->config, $id, $getPolygon, $maxOffset, output: $output);
    }

    /**
     * 行政区划搜索
     * @param string $keyword 搜索关键词
     * @param int|null $getPolygon 返回行政区划轮廓点串
     * @param int|null $maxOffset 轮廓点串的抽稀精度
     * @param string|null $output 返回格式：支持JSON/JSONP，默认JSON
     * @return array
     */
    public function districtSearch(string  $keyword, ?int $getPolygon = 0, ?int $maxOffset = null,
                                   ?string $output = 'json'): array
    {
        return District::search($this->config, $keyword, $getPolygon, $maxOffset, output: $output);
    }
}