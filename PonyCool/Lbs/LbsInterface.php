<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/6/14
 * Time: 22:44
 */
declare(strict_types=1);

namespace PonyCool\Lbs;


interface LbsInterface
{
    /**
     * 检查配置
     * @param Config $config
     * @return bool
     */
    public function checkConfig(Config $config): bool;

    /**
     * 签名
     * @param string $url
     * @param array|null $data
     * @return string
     */
    public function sign(string $url, ?array $data = null): string;

    /**
     * IP定位
     * @param string $ip
     * @return array
     */
    public function ip(string $ip): array;

    /**
     * 逆地址解析
     * 本接口提供由经纬度到文字地址及相关位置信息的转换能力
     * @param string $location 经纬度（GCJ02坐标系）
     * @param string|null $output 返回格式
     * @return array
     */
    public function locationToAddress(string $location, ?string $output = 'json'): array;

    /**
     * 地址解析
     * @param string $address 地址（注：地址中请包含城市名称，否则会影响解析效果）
     * @param string|null $region 地址所在城市（若地址中包含城市名称侧可不传）
     * @param string|null $output 返回格式：支持JSON/JSONP，默认JSON
     * @return array
     */
    public function addressToLocation(string $address, ?string $region = null, ?string $output = 'json'): array;

    /**
     * 智能地址解析
     * @param string $address 地址
     * @param string|null $region 地址所在城市
     * @param string|null $output 返回格式：支持JSON/JSONP，默认JSON
     * @return array
     */
    public function smartAddress(string $address, ?string $region = null, ?string $output = 'json'): array;

    /**
     * 获取省市区列表
     * @param string|null $output 返回格式：支持JSON/JSONP，默认JSON
     * @return array
     */
    public function getDistrictList(?string $output = 'json'): array;

    /**
     * 行政区划搜索
     * @param string $keyword 搜索关键词
     * @param int|null $getPolygon 返回行政区划轮廓点串
     * @param int|null $maxOffset 轮廓点串的抽稀精度
     * @param string|null $output 返回格式：支持JSON/JSONP，默认JSON
     * @return array
     */
    public function districtSearch(string  $keyword, ?int $getPolygon = 0, ?int $maxOffset = null,
                                   ?string $output = 'json'): array;
}