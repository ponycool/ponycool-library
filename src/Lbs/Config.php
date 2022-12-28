<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/6/14
 * Time: 22:44
 */
declare(strict_types=1);

namespace PonyCool\Lbs;


class Config
{
    protected string $source;
    protected string $key;
    //是否开启签名
    protected bool $sign;
    protected ?string $secretKey;

    public function __construct()
    {
        $this->sign = false;
        $this->secretKey = null;
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @param string $source
     * @return Config
     */
    public function setSource(string $source): Config
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     * @return Config
     */
    public function setKey(string $key): Config
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSign(): bool
    {
        return $this->sign;
    }

    /**
     * @param bool $sign
     * @return Config
     */
    public function setSign(bool $sign): Config
    {
        $this->sign = $sign;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSecretKey(): ?string
    {
        return $this->secretKey;
    }

    /**
     * @param string|null $secretKey
     * @return Config
     */
    public function setSecretKey(?string $secretKey): Config
    {
        $this->secretKey = $secretKey;
        return $this;
    }


}