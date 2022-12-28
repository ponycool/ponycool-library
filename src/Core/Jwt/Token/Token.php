<?php

namespace PonyCool\Core\Jwt\Token;
abstract class Token
{
    // 密钥
    public string $secret;
    // 签名算法
    public string $alg;
    // 令牌类型
    public string $typ;
    // issuer 签发人
    public string $iss;
    // expiration time 过期时间
    public int $exp;
    // subject 主题
    public string $sub;
    // audience 受众
    public string $aud;
    // Not Before 生效时间
    public int $nbf;
    // Issued At 签发时间
    public int $iat;
    // JWT ID 编号
    public ?string $jti;
    // 用户名
    public string $name;
    // 用户UID
    public string $uid;
    // 用户账户全局ID
    public ?int $aid;
    // 用户全局ID
    public ?int $gid;
    // 管理用户
    public string $admin;
    // 头部
    public array $header;
    // 有效负载
    public array $payload;
    // 签名
    public string $signature;
    // token
    public string $token;

    abstract public function get(): string;

    abstract public function verify(string $secret, string $token): bool;
}
