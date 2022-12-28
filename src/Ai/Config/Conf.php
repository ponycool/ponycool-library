<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/6/28
 * Time: 9:49 上午
 */
declare(strict_types=1);

namespace PonyCool\Ai\Config;

class Conf
{
    // 源
    protected string $source;
    protected string $secretId;
    protected string $secretKey;
    protected ?string $appCode;
    // 地域参数
    protected ?string $region;
    protected ?string $endpoint;
    protected ?string $imageBase64;
    protected ?string $imageUrl;

    public function __construct()
    {
        $this->setAppCode(null)
            ->setRegion(null)
            ->setEndpoint(null)
            ->setImageUrl(null)
            ->setImageBase64(null);
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
     * @return Conf
     */
    public function setSource(string $source): Conf
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @return string
     */
    public function getSecretId(): string
    {
        return $this->secretId;
    }

    /**
     * @param string $secretId
     * @return Conf
     */
    public function setSecretId(string $secretId): Conf
    {
        $this->secretId = $secretId;
        return $this;
    }

    /**
     * @return string
     */
    public function getSecretKey(): string
    {
        return $this->secretKey;
    }

    /**
     * @param string $secretKey
     * @return Conf
     */
    public function setSecretKey(string $secretKey): Conf
    {
        $this->secretKey = $secretKey;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAppCode(): ?string
    {
        return $this->appCode;
    }

    /**
     * @param string|null $appCode
     * @return Conf
     */
    public function setAppCode(?string $appCode): Conf
    {
        $this->appCode = $appCode;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRegion(): ?string
    {
        return $this->region;
    }

    /**
     * @param string|null $region
     * @return Conf
     */
    public function setRegion(?string $region): Conf
    {
        $this->region = $region;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEndpoint(): ?string
    {
        return $this->endpoint;
    }

    /**
     * @param string|null $endpoint
     * @return Conf
     */
    public function setEndpoint(?string $endpoint): Conf
    {
        $this->endpoint = $endpoint;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getImageBase64(): ?string
    {
        return $this->imageBase64;
    }

    /**
     * @param string|null $imageBase64
     * @return Conf
     */
    public function setImageBase64(?string $imageBase64): Conf
    {
        $this->imageBase64 = $imageBase64;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    /**
     * @param string|null $imageUrl
     * @return Conf
     */
    public function setImageUrl(?string $imageUrl): Conf
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }
}