<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/6/30
 * Time: 10:11 上午
 */
declare(strict_types=1);

namespace PonyCool\Mq;

class Config
{
    // 驱动
    protected string $driver;
    protected string $host;
    // 默认端口
    protected int $port;
    protected string $user;
    protected string $pass;

    public function __construct()
    {
        $this->port = 0;
    }

    /**
     * @return string
     */
    public function getDriver(): string
    {
        return $this->driver;
    }

    /**
     * @param string $driver
     * @return Config
     */
    public function setDriver(string $driver): Config
    {
        $this->driver = $driver;
        return $this;
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
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @param string $user
     * @return Config
     */
    public function setUser(string $user): Config
    {
        $this->user = $user;
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

}