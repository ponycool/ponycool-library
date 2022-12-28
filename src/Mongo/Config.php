<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/7/7
 * Time: 3:46 下午
 */
declare(strict_types=1);

namespace PonyCool\Mongo;

class Config
{
    private string $host;
    private int $port;
    private string $user;
    private string $pass;
    private string $db;

    public function __construct()
    {
        $this->port = 27017;
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

    /**
     * @return string
     */
    public function getDb(): string
    {
        return $this->db;
    }

    /**
     * @param string $db
     * @return Config
     */
    public function setDb(string $db): Config
    {
        $this->db = $db;
        return $this;
    }
}