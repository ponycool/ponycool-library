<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/6/14
 * Time: 23:24
 */
declare(strict_types=1);

namespace PonyCool\Lbs\Tencent\Request;

use GuzzleHttp\Client;
use PonyCool\Lbs\Config;
use PonyCool\Lbs\Tencent\Signature\Sign;
use GuzzleHttp\Exception\GuzzleException;
use Exception;

class Get
{
    /**
     * 执行GET请求
     */
    public static function exec(Config $config, string $url): array
    {
        //如果URL被编码，则先解码
        $url = urldecode($url) === $url ? $url : urldecode($url);

        if ($config->isSign()) {
            $sign = Sign::get($config->getSecretKey(), $url);
            $url .= '&sig=' . $sign;
        }
        // 重新url编码参数
        $segments = explode('?', $url);
        if (count($segments) > 0) {
            $url = sprintf('%s?%s',
                $segments[0],
                urlencode($segments[1])
            );
        }

        try {
            $client = new Client(
                [
                    'verify' => false
                ]
            );
            $response = $client->get($url);
            if ($response->getStatusCode() !== 200) {
                throw new Exception('LBS请求失败，请求方法GET');
            }
            $res = $response->getBody()->getContents();
            list($status, $data) = [true, json_decode($res, true)];
        } catch (Exception $e) {
            list($status, $data) = [false, $e->getMessage()];
        } catch (GuzzleException $e) {
            list($status, $data) = [false, $e->getMessage()];
        }
        return [$status, $data];
    }
}