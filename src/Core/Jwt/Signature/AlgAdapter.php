<?php
declare(strict_types=1);

namespace PonyCool\Core\Jwt\Signature;

use PonyCool\Core\Jwt\Json\Json;
use PonyCool\Core\Jwt\Base64\Base64Url;

class AlgAdapter implements AlgInterface
{
    protected object $alg;
    protected string $secret;
    protected string $header;
    protected string $payload;

    /**
     * @return mixed
     */
    public function getAlg(): object
    {
        return $this->alg;
    }

    /**
     * @param object $alg
     */
    public function setAlg(object $alg): void
    {
        $this->alg = $alg;
    }

    /**
     * @return mixed
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     */
    public function setSecret(string $secret): void
    {
        $this->secret = $secret;
    }

    /**
     * @return string
     */
    public function getHeader(): string
    {
        return $this->header;
    }

    /**
     * @param string $header
     */
    public function setHeader(string $header): void
    {
        $this->header = $header;
    }

    /**
     * @return string
     */
    public function getPayload(): string
    {
        return $this->payload;
    }

    /**
     * @param string $payload
     */
    public function setPayload(string $payload): void
    {
        $this->payload = $payload;
    }


    public function __construct(object $alg)
    {
        $this->setAlg($alg);
    }

    /**
     * 初始化算法适配器
     * @param string $secret
     * @param array $header
     * @param array $payload
     */
    public function init(string $secret, array $header, array $payload): void
    {
        $this->setSecret($secret);
        $this->setHeader(Base64Url::base64UrlEncode(Json::jsonEncode($header)));
        $this->setPayload(Base64Url::base64UrlEncode(Json::jsonEncode($payload)));
    }

    /**
     * 加密
     * @return string
     */
    public function encrypt(): string
    {
        $alg = $this->getAlg();
        $raw = [
            $this->getHeader(),
            $this->getPayload()
        ];
        $raw = implode(".", $raw);
        $signature = $alg->encrypt($this->getSecret(), $raw);
        return Base64Url::base64UrlEncode($signature);
    }

    public function decrypt(): array
    {
        return [];
        // TODO: Implement decrypt() method.
    }

}
