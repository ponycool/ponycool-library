<?php
declare(strict_types=1);

namespace PonyCool\Core\Jwt\Token;

use Exception;
use PonyCool\Core\Jwt\Json\Json;
use PonyCool\Core\Jwt\Signature\Signature;
use PonyCool\Core\Jwt\Base64\Base64Url;
use PonyCool\Core\Jwt\Validation\ValidationStrategy;
use PonyCool\Core\Jwt\Exception\TokenException;
use PonyCool\Core\Jwt\Exception\ValueException;
use PonyCool\Core\Jwt\Exception\ExpiredException;
use PonyCool\Core\Jwt\Exception\ArgumentException;
use PonyCool\Core\Jwt\Exception\SignatureException;
use PonyCool\Core\Jwt\Exception\MethodCallException;
use PonyCool\Core\Jwt\Exception\BeforeValidException;

class JwtToken extends Token
{
    /**
     * 获取Token
     * @return string
     */
    public function get(): string
    {
        $header = [
            'alg' => $this->getAlg(),
            'typ' => $this->getTyp(),
        ];
        $this->setHeader($header);

        $payload = [
            'iss' => $this->getIss(),
            'exp' => $this->getExp(),
            'sub' => $this->getSub(),
            'aud' => $this->getAud(),
            'nbf' => $this->getNbf(),
            'iat' => $this->getIat(),
        ];

        // 处理扩展有效载荷
        $extendPayload = [
            'jti',
            'admin',
            'account_name',
            'account_id',
            'account_gid',
            'username',
            'user_id',
            'user_gid',
            'uid'
        ];
        foreach ($extendPayload as $item) {
            $method = str_replace('_', ' ', $item);
            $method = 'get' . ucwords(str_replace(' ', '', $method));
            if (!is_null($this->$method())) {
                $payload[$item] = $this->$method();
            }
        }

        $this->setPayload($payload);
        $signatureObj = new Signature();
        $signature = $signatureObj->generate($this->getSecret(), $this->getHeader(), $this->getPayload());
        $tokenArr = [
            Base64Url::base64UrlEncode(Json::jsonEncode($this->getHeader())),
            Base64Url::base64UrlEncode(Json::jsonEncode($this->getPayload())),
            $signature
        ];
        return implode(".", $tokenArr);
    }

    /**
     * TOKEN 验证
     * @param string $secret
     * @param string $token
     * @return bool
     * @throws Exception
     */
    public function verify(string $secret, string $token): bool
    {
        if (empty($secret)) {
            throw new ArgumentException("密钥无效");
        }
        $this->setSecret($secret);
        $token = explode(".", $token);
        if (3 !== count($token)) {
            throw new TokenException("不合法的TOKEN");
        }
        $header = Json::jsonDecode(Base64Url::base64UrlDecode($token[0]));
        $payload = Json::jsonDecode(Base64Url::base64UrlDecode($token[1]));
        $rawSignature = Base64Url::base64UrlDecode($token[2]);
        if (false === $rawSignature) {
            throw new ValueException("签名解码失败");
        }
        if (empty($header['alg'])) {
            throw new ValueException("签名算法错误");
        }
        if (empty($header['typ']) || 'JWT' !== $header['typ']) {
            throw new ValueException("令牌类型错误");
        }
        $this->setAlg($header['alg']);
        $this->setTyp($header['typ']);
        $validation = new ValidationStrategy();
        $validation->setStrategy('timestamp');
        $timestampArr = [
            'exp' => '过期时间',
            'nbf' => '生效时间',
            'iat' => '签发时间',
        ];
        foreach ($payload as $k => $v) {
            if (key_exists($k, $timestampArr)) {
                $res = $validation->validator((string)$v);
                if (!$res) {
                    throw new ValueException($timestampArr[$k] . "时间格式错误");
                }
            }
            if ($k === 'admin') {
                if (!is_bool($v)) {
                    throw new ValueException("Admin类型错误");
                }
            }
            $method = str_replace('_', ' ', $k);
            $method = 'set' . ucwords(str_replace(' ', '', $method));
            if (!method_exists($this, $method)) {
                throw new MethodCallException("JWT有效负载存在无效的属性");
            }
            $this->$method($v);
        }
        $timestamp = time();
        // 生效时间之前不接收处理该token
        if ($this->getNbf() > $timestamp) {
            throw new BeforeValidException(
                date('Y-m-d\TH:i:sO', $this->getNbf()) . "之前无法处理令牌"
            );
        }
        // 签发时间大于当前服务器时间验证失败
        if ($this->getIat() > $timestamp) {
            throw new BeforeValidException(
                date('Y-m-d\TH:i:sO', $this->getIat()) . "之前无法处理令牌"
            );
        }
        // 过期时间小于、等于当前服务器时间验证失败
        if ($timestamp >= $this->getExp()) {
            throw new ExpiredException("令牌过期");
        }
        $this->setHeader($header);
        $this->setPayload($payload);
        // 验证签名
        $rawSignature = Base64Url::base64UrlEncode($rawSignature);
        $signatureObj = new Signature();
        $res = $signatureObj->verify($this->getSecret(), $this->getHeader(), $this->getPayload(), $rawSignature);
        if (!$res) {
            throw new SignatureException("签名验证失败");
        }
        return true;
    }
}
