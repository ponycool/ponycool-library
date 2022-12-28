<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/12/29
 * Time: 11:15 上午
 */
declare(strict_types=1);

namespace PonyCool\Es;

use Exception;
use Carbon\Carbon;
use Elasticsearch\Client;

class Document
{
    /**
     * 索引文档
     * @param Client $client ES客户端
     * @param string $index 索引
     * @param string|array $data 数据
     * @return bool|string
     */
    public static function index(Client $client, string $index, string|array $data): bool|string
    {
        $params = [
            'index' => $index,
            'type' => '_doc',
            'body' => [
                '@timestamp' => Carbon::now()->toIso8601String()
            ]
        ];
        if (is_string($data)) {
            $params['body'] = array_merge($params['body'], [
                'message' => $data
            ]);
        }
        if (is_array($data)) {
            $params['body'] = array_merge($params['body'], $data);
        }
        try {
            $client->index($params);
        } catch (Exception $exc) {
            return $exc->getMessage();
        }
        return true;
    }
}