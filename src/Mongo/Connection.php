<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/7/7
 * Time: 5:06 下午
 */
declare(strict_types=1);

namespace PonyCool\Mongo;

use MongoDB\Client;

class Connection
{

    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * 获取连接客户端
     * @return Client
     */
    public function connect(): Client
    {
        $connStr = $this->getConnStr();
        return new Client($connStr);
    }

    /**
     * 获取连接字符串
     * @return string
     */
    private function getConnStr(): string
    {
        $connStr = "mongodb://";
        $connStr .= sprintf('%s:%s@%s:%u',
            $this->config->getUser(),
            $this->config->getPass(),
            $this->config->getHost(),
            $this->config->getPort()
        );
        return $connStr;
    }
}