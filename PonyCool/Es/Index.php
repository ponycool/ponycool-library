<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/12/29
 * Time: 11:22 上午
 */
declare(strict_types=1);

namespace PonyCool\Es;

use Exception;
use Elasticsearch\Client;

class Index
{
    /**
     * 创建索引
     * @param Client $client ES客户端
     * @param string $index 索引
     * @param int $shards 分片数量
     * @param int $replicas 副本数量
     * @return bool|string
     */
    public static function create(Client $client, string $index, int $shards = 1, int $replicas = 0): bool|string
    {
        $params = [
            'client' => [
                'timeout' => 2,
                'connect_timeout' => 2
            ]
        ];
        if ($client->ping($params) != true) {
            return 'Elasticsearch 连接失败';
        }
        $params = [
            'index' => $index,
        ];
        if ($client->indices()->exists($params) === false) {
            $params['body'] = [
                'settings' => [
                    'number_of_shards' => $shards,
                    'number_of_replicas' => $replicas
                ],
                'mappings' => [
                    '_source' => [
                        'enabled' => true
                    ],
                    'properties' => [
                        '@timestamp' => [
                            'type' => 'date'
                        ]
                    ]
                ]
            ];
            try {
                $client->indices()->create($params);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
        return true;
    }
}