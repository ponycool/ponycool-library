<?php
declare(strict_types=1);

namespace PonyCool\Core\Jwt\Token;

use DateTime;
use PonyCool\Core\Jwt\Json\Json;
use PonyCool\Core\Jwt\Signature\Signature;
use PonyCool\Core\Jwt\Base64\Base64Url;
use PonyCool\Core\Jwt\Validation\ValidationStrategy;
use PonyCool\Core\Jwt\Exception\{TokenException,
    ValueException,
    ExpiredException,
    ArgumentException,
    SignatureException,
    MethodCallException,
    BeforeValidException
};
use ReflectionException;

class JwtToken extends Token
{
    /**
     * @return string
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
    public function getAlg(): string
    {

        return $this->alg ?? 'HS256';
    }

    /**
     * @param string $alg
     */
    public function setAlg(string $alg): void
    {
        $this->alg = $alg;
    }

    /**
     * @return string
     */
    public function getTyp(): string
    {
        return $this->typ ?? 'JWT';
    }

    /**
     * @param string $typ
     */
    public function setTyp(string $typ): void
    {
        $this->typ = $typ;
    }


    /**
     * @return string
     */
    public function getIss(): string
    {
        $issuer = 'PonyCool';
        return $this->iss ?? $issuer;
    }

    /**
     * @param string $iss
     */
    public function setIss(string $iss): void
    {
        $this->iss = $iss;
    }

    /**
     * 过期时间，默认为签发时间+2小时
     * @return int
     */
    public function getExp(): int
    {
        return $this->exp ?? strtotime("+2 hours");
    }

    /**
     * @param int $exp
     */
    public function setExp(int $exp): void
    {
        $this->exp = $exp;
    }

    /**
     * @return string
     */
    public function getSub(): string
    {
        $subject = 'authenticate';
        return $this->sub ?? $subject;
    }

    /**
     * @param string $sub
     */
    public function setSub(string $sub): void
    {
        $this->sub = $sub;
    }

    /**
     * @return string
     */
    public function getAud(): string
    {
        $audience = 'user';
        return $this->aud ?? $audience;
    }

    /**
     * @param string $aud
     */
    public function setAud(string $aud): void
    {
        $this->aud = $aud;
    }

    /**
     * @return int
     */
    public function getNbf(): int
    {
        return $this->nbf ?? time();
    }

    /**
     * @param int $nbf
     */
    public function setNbf(int $nbf): void
    {
        $this->nbf = $nbf;
    }

    /**
     * @return int
     */
    public function getIat(): int
    {
        return $this->iat ?? time();
    }

    /**
     * @param int $iat
     */
    public function setIat(int $iat): void
    {
        $this->iat = $iat;
    }

    /**
     * @return string
     */
    public function getJti(): ?string
    {
        return $this->jti ?? null;
    }

    /**
     * @param string|null $jti
     */
    public function setJti(?string $jti): void
    {
        $this->jti = $jti;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getUid(): string
    {
        return $this->uid;
    }

    /**
     * @param string $uid
     */
    public function setUid(string $uid): void
    {
        $this->uid = $uid;
    }

    /**
     * @return int|null
     */
    public function getAid(): ?int
    {
        return $this->aid;
    }

    /**
     * @param int|null $aid
     */
    public function setAid(?int $aid): void
    {
        $this->aid = $aid;
    }

    /**
     * @return int|null
     */
    public function getGid(): ?int
    {
        return $this->gid;
    }

    /**
     * @param int|null $gid
     */
    public function setGid(?int $gid): void
    {
        $this->gid = $gid;
    }

    /**
     * @return string
     */
    public function getAdmin(): string
    {
        return $this->admin ?? 'false';
    }

    /**
     * @param string $admin
     */
    public function setAdmin(string $admin): void
    {
        $this->admin = $admin;
    }

    /**
     * @return array
     */
    public function getHeader(): array
    {
        return $this->header;
    }

    /**
     * @param array $header
     */
    public function setHeader(array $header): void
    {
        $this->header = $header;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * @param array $payload
     */
    public function setPayload(array $payload): void
    {
        $this->payload = $payload;
    }

    /**
     * @return string
     */
    public function getSignature(): string
    {
        return $this->signature;
    }

    /**
     * @param string $signature
     */
    public function setSignature(string $signature): void
    {
        $this->signature = $signature;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }


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
            'name' => $this->getName(),
            'uid' => $this->getUid(),
            'aid' => $this->getAid(),
            'gid' => $this->getGid(),
            'admin' => $this->getAdmin(),
            'aud' => $this->getAud(),
            'nbf' => $this->getNbf(),
            'iat' => $this->getIat(),
            'jti' => $this->getJti(),
        ];
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
     * @throws ReflectionException
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
        if (is_null($header)) {
            throw new ValueException("头部解码失败");
        }
        if (is_null($payload)) {
            throw new ValueException("有效负载解码失败");
        }
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
                if (!in_array($v, ['true', 'false'], true)) {
                    throw new ValueException("Admin类型错误");
                }
            }
            $method = 'set' . ucfirst(strtolower($k));
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
