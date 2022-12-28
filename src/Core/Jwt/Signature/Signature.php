<?php
declare(strict_types=1);

namespace PonyCool\Core\Jwt\Signature;

use PonyCool\Core\Jwt\Exception\AlgException;

class Signature
{
    /**
     * 生成签名
     * @param string $secret
     * @param array $header
     * @param array $payload
     * @return string
     */
    public function generate(string $secret, array $header, array $payload): string
    {
        $alg = $this->loadAlg($header['alg']);
        $algAdapter = new AlgAdapter($alg);
        $algAdapter->init($secret, $header, $payload);
        return $algAdapter->encrypt();
    }


    /**
     * 签名验证
     * @param string $secret
     * @param array $header
     * @param array $payload
     * @param string $rawSignature
     * @return bool
     */
    public function verify(string $secret, array $header, array $payload, string $rawSignature): bool
    {
        $signature = $this->generate($secret, $header, $payload);
        return $rawSignature === $signature;
    }


    /**
     * 载入加密算法
     * @param string $alg
     * @return object
     */
    protected function loadAlg(string $alg): object
    {
        $alg = ucfirst(strtolower($alg));
        $algClass = __NAMESPACE__ . '\\' . $alg;
        if (!class_exists($algClass)) {
            throw new AlgException("JWT签名加密算法不存在");
        }
        return new $algClass;
    }
}
