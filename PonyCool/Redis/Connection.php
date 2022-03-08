<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/7/9
 * Time: 3:56 下午
 */
declare(strict_types=1);

namespace PonyCool\Redis;

use Redis;
use Exception;

class Connection
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * 获取连接客户端
     * @return Redis
     * @throws Exception
     */
    public function connect(): Redis
    {
        try {
            $redis = new Redis();
            $redis->connect($this->config->getHost(), $this->config->getPort());
            $redis->auth($this->config->getPass());
            return $redis;
        } catch (Exception $e) {
            throw new Exception(sprintf('Redis连接异常，error：%s', $e->getMessage()));
        }
    }
}