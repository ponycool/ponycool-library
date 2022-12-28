<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/7/9
 * Time: 2:51 下午
 */
declare(strict_types=1);

namespace PonyCool\Redis;

class Config
{
    private string $host;
    private int $port;
    private string $pass;
    private int $db;

    public function __construct()
    {
        $this->port = 6379;
        $this->db = 0;

    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     * @return Config
     */
    public function setHost(string $host): Config
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @param int $port
     * @return Config
     */
    public function setPort(int $port): Config
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @return string
     */
    public function getPass(): string
    {
        return $this->pass;
    }

    /**
     * @param string $pass
     * @return Config
     */
    public function setPass(string $pass): Config
    {
        $this->pass = $pass;
        return $this;
    }

    /**
     * @return int
     */
    public function getDb(): int
    {
        return $this->db;
    }

    /**
     * @param int $db
     * @return Config
     */
    public function setDb(int $db): Config
    {
        $this->db = $db;
        return $this;
    }
}