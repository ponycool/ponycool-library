<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/12/23
 * Time: 5:01 下午
 */
declare(strict_types=1);

namespace PonyCool\Es;

class Config
{
    protected array $hosts;
    protected string $host;
    protected int $port;
    protected string $scheme;
    protected string $user;
    protected string $pass;

    public function __construct()
    {
        $this->scheme = 'http';
        $this->host = '';
        $this->port = 9200;
        $this->hosts = [];
        $this->user = '';
        $this->pass = '';
    }

    /**
     * @return array
     */
    public function getHosts(): array
    {
        return $this->hosts;
    }

    /**
     * @param array $hosts
     * @return Config
     */
    public function setHosts(array $hosts): Config
    {
        $this->hosts = $hosts;
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
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * @param string $scheme
     * @return Config
     */
    public function setScheme(string $scheme): Config
    {
        $this->scheme = $scheme;
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