<?php
declare(strict_types=1);

namespace PonyCool\Core\Jwt\Token;

abstract class Token
{
    // 密钥
    protected string $secret;
    // 签名算法
    protected string $alg;
    // 令牌类型
    protected string $typ;
    // issuer 签发人
    protected string $iss;
    // expiration time 过期时间
    protected int $exp;
    // subject 主题
    protected string $sub;
    // audience 受众
    protected string $aud;
    // Not Before 生效时间
    protected int $nbf;
    // Issued At 签发时间
    protected int $iat;
    // JWT ID 编号
    protected ?string $jti;
    // 账户名称
    protected ?string $accountName;
    // 账户ID
    protected ?int $accountId;
    // 账户全局ID
    protected ?int $accountGid;
    // 用户名
    protected ?string $userName;
    // 用户ID
    protected ?int $userId;
    // 用户全局ID
    protected ?int $userGid;
    // 用户UID
    protected ?string $uid;
    // 管理用户
    protected ?string $admin;
    // 头部
    protected array $header;
    // 有效负载
    protected array $payload;
    // 签名
    protected string $signature;
    // token
    protected string $token;

    public function __construct()
    {
        $this->setJti(null);
        $this->setAccountName(null);
        $this->setAccountId(null);
        $this->setAccountGid(null);
        $this->setUserName(null);
        $this->setUserId(null);
        $this->setUserGid(null);
        $this->setUid(null);
        $this->setAdmin(null);
    }

    /**
     * 密钥
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
     * 签名算法
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
     * 令牌类型
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
     * 签发人
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
     * 主题
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
     * 受众
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
     * 生效时间
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
     * 签发时间
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
     * JWT ID 编号
     * @return string|null
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
     * 账户名称
     * @return string|null
     */
    public function getAccountName(): ?string
    {
        return $this->accountName;
    }

    /**
     * 设置账户名称
     * @param string|null $accountName
     */
    public function setAccountName(?string $accountName): void
    {
        $this->accountName = $accountName;
    }

    /**
     * @return int|null
     */
    public function getAccountId(): ?int
    {
        return $this->accountId;
    }

    /**
     * @param int|null $accountId
     */
    public function setAccountId(?int $accountId): void
    {
        $this->accountId = $accountId;
    }

    /**
     * @return int|null
     */
    public function getAccountGid(): ?int
    {
        return $this->accountGid;
    }

    /**
     * @param int|null $accountGid
     */
    public function setAccountGid(?int $accountGid): void
    {
        $this->accountGid = $accountGid;
    }

    /**
     * 用户名称
     * @return string|null
     */
    public function getUserName(): ?string
    {
        return $this->userName;
    }

    /**
     * 设置用户名称
     * @param string|null $userName
     */
    public function setUserName(?string $userName): void
    {
        $this->userName = $userName;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @param int|null $userId
     */
    public function setUserId(?int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return int|null
     */
    public function getUserGid(): ?int
    {
        return $this->userGid;
    }

    /**
     * @param int|null $userGid
     */
    public function setUserGid(?int $userGid): void
    {
        $this->userGid = $userGid;
    }

    /**
     * @return string|null
     */
    public function getUid(): ?string
    {
        return $this->uid;
    }

    /**
     * @param string|null $uid
     */
    public function setUid(?string $uid): void
    {
        $this->uid = $uid;
    }

    /**
     * @return string|null
     */
    public function getAdmin(): ?string
    {
        return $this->admin;
    }

    /**
     * @param string|null $admin
     */
    public function setAdmin(?string $admin): void
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

    abstract public function get(): string;

    abstract public function verify(string $secret, string $token): bool;
}
