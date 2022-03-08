<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/6/15
 * Time: 0:37
 */
declare(strict_types=1);

namespace PonyCool\Lbs\Tencent\Request;


use Exception;
use PonyCool\Lbs\Config;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use PonyCool\Lbs\Tencent\Signature\Sign;

class Post
{
    /**
     * 执行POST请求
     * @param Config $config
     * @param string $url
     * @param array|null $data
     * @return array
     */
    public static function exec(Config $config, string $url, ?array $data = null): array
    {
        if ($config->isSign()) {
            $sign = Sign::getPostSign($config->getSecretKey(), $url, $data);
            $url .= '?sig=' . $sign;
        }
        try {
            $headers = [
                'Content-Type' => 'application/json'
            ];
            $client = new Client(
                [
                    'headers' => $headers,
                    'verify' => false,
                ]
            );
            $options = [
                'body' => json_encode($data, 320),
            ];
            $response = $client->post($url, $options);
            if ($response->getStatusCode() !== 200) {
                throw new Exception('LBS请求失败，请求方法POST');
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