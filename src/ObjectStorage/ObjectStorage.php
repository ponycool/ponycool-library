<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/5/25
 * Time: 8:58 上午
 */

namespace PonyCool\ObjectStorage;


use Exception;

class ObjectStorage
{
    protected string $access_key;
    protected string $secret;
    protected string $region;
    protected string $bucket;
    protected ?string $domain;

    // 上传回调服务器的URL，只用于前端签名上传
    protected ?string $callbackUrl;
    // 上传时指定的前缀
    protected ?string $prefix;
    // 设置该policy超时时间是30s. 即这个policy过了这个有效时间，将不能访问，用于前端签名上传
    protected int $signatureExpires;

    public function __construct()
    {
        // 初始化属性
        $this->setDomain(null)
            ->setCallbackUrl(null)
            ->setPrefix(null)
            ->setSignatureExpires(30);
    }

    /**
     * @return string
     */
    public function getAccessKey(): string
    {
        return $this->access_key;
    }

    /**
     * @param string $access_key
     * @return ObjectStorage
     */
    public function setAccessKey(string $access_key): ObjectStorage
    {
        $this->access_key = $access_key;
        return $this;
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     * @return ObjectStorage
     */
    public function setSecret(string $secret): ObjectStorage
    {
        $this->secret = $secret;
        return $this;
    }

    /**
     * @return string
     */
    public function getRegion(): string
    {
        return $this->region;
    }

    /**
     * @param string $region
     * @return ObjectStorage
     */
    public function setRegion(string $region): ObjectStorage
    {
        $this->region = $region;
        return $this;
    }

    /**
     * @return string
     */
    public function getBucket(): string
    {
        return $this->bucket;
    }

    /**
     * @param string $bucket
     * @return ObjectStorage
     */
    public function setBucket(string $bucket): ObjectStorage
    {
        $this->bucket = $bucket;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDomain(): ?string
    {
        return $this->domain;
    }

    /**
     * @param string|null $domain
     * @return ObjectStorage
     */
    public function setDomain(?string $domain): ObjectStorage
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCallbackUrl(): ?string
    {
        return $this->callbackUrl;
    }

    /**
     * @param string|null $callbackUrl
     * @return ObjectStorage
     */
    public function setCallbackUrl(?string $callbackUrl): ObjectStorage
    {
        $this->callbackUrl = $callbackUrl;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    /**
     * @param string|null $prefix
     * @return ObjectStorage
     */
    public function setPrefix(?string $prefix): ObjectStorage
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * @return int
     */
    public function getSignatureExpires(): int
    {
        return $this->signatureExpires;
    }

    /**
     * @param int $signatureExpires
     * @return ObjectStorage
     */
    public function setSignatureExpires(int $signatureExpires): ObjectStorage
    {
        $this->signatureExpires = $signatureExpires;
        return $this;
    }

    /**
     * 检查配置
     * @return bool
     * @throws Exception
     */
    public function check(): bool
    {
        try {
            if (strlen($this->getAccessKey()) === 0) {
                throw new Exception('未配置有效的AccessKey');
            }
            if (strlen($this->getSecret()) === 0) {
                throw new Exception('未配置有效的Secret');
            }
            if (strlen($this->getRegion()) === 0) {
                throw new Exception('未配置有效的Region或Endpoint');
            }
            if (strlen($this->getBucket()) === 0) {
                throw new Exception('未配置有效的Bucket');
            }
            return true;
        } catch (Exception $e) {
            $message = sprintf('Object Storage 配置无效，%s', $e->getMessage());
            throw new Exception($message);
        }
    }
}