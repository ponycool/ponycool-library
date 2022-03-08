<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/12/28
 * Time: 2:13 下午
 */
declare(strict_types=1);

namespace PonyCool\Es;

use Elasticsearch\{ClientBuilder, Client as EsClient};

class Client
{
    private array $hosts;
    private EsClient $builder;

    public function __construct(Config $conf)
    {
        $this->hosts = $conf->getHosts();
        if (empty($this->hosts)) {
            if (!empty($conf->getHost())) {
                $this->hosts = [
                    [
                        'host' => $conf->getHost(),
                        'port' => (string)$conf->getPort(),
                        'scheme' => $conf->getScheme()
                    ]
                ];
                !$conf->getUser() ?: $this->hosts[0]['user'] = $conf->getUser();
                !$conf->getPass() ?: $this->hosts[0]['pass'] = $conf->getPass();
            }
        }
        if (!empty($this->hosts)) {
            $this->builder = ClientBuilder::create()
                ->setHosts($this->hosts)
                ->build();
        }
    }

    /**
     * 创建索引
     * @param string $index 索引
     * @param int $shards 分片数量
     * @param int $replicas 副本数量
     * @return bool|string
     */
    public function createIndex(string $index, int $shards = 1, int $replicas = 0): bool|string
    {
        return Index::create($this->builder, $index, $shards, $replicas);
    }

    /**
     * 索引文档
     * @param string $index 索引
     * @param string|array $data 数据
     * @return bool|string
     */
    public function index(string $index, string|array $data): bool|string
    {
        $res = $this->createIndex($index);
        if ($res !== true) {
            return $res;
        }
        return Document::index($this->builder, $index, $data);
    }
}