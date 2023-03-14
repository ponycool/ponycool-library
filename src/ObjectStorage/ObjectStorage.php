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

    public function __construct()
    {
        $this->setDomain(null);
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
     * 检查配置
     * @return bool
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
            log_message(
                'error',
                'Object Storage 配置无效，error：{error}',
                ['error' => $e->getMessage()]);
            return false;
        }
    }
}