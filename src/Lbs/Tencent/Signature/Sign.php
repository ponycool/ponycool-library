<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/6/14
 * Time: 23:30
 */
declare(strict_types=1);

namespace PonyCool\Lbs\Tencent\Signature;

class Sign
{
    /**
     * 获取签名
     * @param string $sk 密钥
     * @param string $url 请求地址
     * @param array|null $data POST请求数据
     * @return string
     */
    public static function get(string $sk, string $url, ?array $data = null): string
    {
        $uri = parse_url($url);
        $query = $uri['query'] ?? null;
        $params = [];
        if (!is_null($query) && is_null($data)) {
            // 解析参数
            parse_str($query, $params);
            // 对参数进行排序：按参数名升序
            ksort($params);
        }
        if (!is_null($data)) {
            ksort($data);
            foreach ($data as &$item) {
                $item = json_encode($item, 320);
            }
            $params = $data;
        }
        // 签名
        return md5(sprintf('%s?%s%s',
            $uri['path'],
            urldecode(http_build_query($params)),
            $sk
        ));
    }

    /**
     * 获取POST接口签名
     * @param string $sk 密钥
     * @param string $url 请求地址
     * @param array|null $data POST请求数据
     * @return string
     */
    public static function getPostSign(string $sk, string $url, ?array $data = null): string
    {
        $uri = parse_url($url);
        $path = $uri['path'] ?? null;
        if (!is_null($data)) {
            ksort($data);
            foreach ($data as &$item) {
                if (is_array($item)) {
                    $item = json_encode($item, 320);
                    $item = str_replace(["\r\n", "\r", "\n"], "", $item);
                }
            }
        }

        return md5(sprintf('%s?%s%s',
            $path,
            urldecode(http_build_query($data)),
            $sk
        ));
    }
}