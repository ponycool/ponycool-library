<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/5/25
 * Time: 4:47 下午
 */
declare(strict_types=1);

namespace PonyCool\Ai\Ocr;

class Config
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
    public function getSecretId(): string
    {
        return $this->secretId;
    }

    /**
     * @param string $secretId
     * @return Config
     */
    public function setSecretId(string $secretId): Config
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
     * @return Config
     */
    public function setSecretKey(string $secretKey): Config
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
     * @return Config
     */
    public function setAppCode(?string $appCode): Config
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
     * @return Config
     */
    public function setRegion(?string $region): Config
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
     * @return Config
     */
    public function setEndpoint(?string $endpoint): Config
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
     * @return Config
     */
    public function setImageBase64(?string $imageBase64): Config
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
     * @return Config
     */
    public function setImageUrl(?string $imageUrl): Config
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

}