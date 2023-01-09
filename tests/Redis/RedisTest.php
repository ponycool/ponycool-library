<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2023/1/9
 * Time: 10:18 AM
 */

namespace Redis;

use Exception;
use PHPUnit\Framework\TestCase;
use PonyCool\Redis\Client;
use PonyCool\Redis\Config;

class RedisTest extends TestCase
{
    /**
     * @test
     * @return Client|null
     */
    public function init(): ?Client
    {
        try {
            $host = '';
            $port = '';
            $pass = '';
            // 初始化配置
            $conf = new Config();
            $conf->setHost($host)
                ->setPort($port)
                ->setPass($pass);
            return new Client($conf);
        } catch (Exception $e) {
            self::assertEquals('', $e->getMessage());
            return null;
        }
    }

    /**
     * @test
     * @depends init
     * @param Client|null $client
     * @return void
     */
    public function ping(?Client $client): void
    {
        self::assertNotNull($client);
        self::assertTrue($client->ping());
    }
}