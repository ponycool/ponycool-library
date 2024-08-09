<?php
/**
 * Created By PhpStorm
 * User: Pony
 * Data: 2024/8/8
 * Time: 下午4:43
 */
declare(strict_types=1);

namespace PonyCool\Email;

use PHPMailer\PHPMailer\PHPMailer;

class Server
{
    // 是否使用SMTP发送
    protected bool $isSMTP;
    // 启用SMTP身份验证
    protected bool $SMTPAuth;
    // 设置要通过的SMTP服务器
    protected string $host;
    // SMTP用户名
    protected string $username;
    // SMTP密码
    protected string $password;
    // 启用密
    protected string $SMTPSecure;
    // 要连接的TCP端口
    protected int $port;

    public function __construct(string $host, string $username, string $password,
                                bool   $isSMTP = true, bool $SMTPAuth = true,
                                string $SMTPSecure = PHPMailer::ENCRYPTION_SMTPS,
                                int    $port = 465)
    {
        $this->isSMTP = $isSMTP;
        $this->SMTPAuth = $SMTPAuth;
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->SMTPSecure = $SMTPSecure;
        $this->port = $port;
    }

    public function isSMTP(): bool
    {
        return $this->isSMTP;
    }

    public function setIsSMTP(bool $isSMTP): Server
    {
        $this->isSMTP = $isSMTP;
        return $this;
    }

    public function isSMTPAuth(): bool
    {
        return $this->SMTPAuth;
    }

    public function setSMTPAuth(bool $SMTPAuth): Server
    {
        $this->SMTPAuth = $SMTPAuth;
        return $this;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function setHost(string $host): Server
    {
        $this->host = $host;
        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): Server
    {
        $this->username = $username;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): Server
    {
        $this->password = $password;
        return $this;
    }

    public function getSMTPSecure(): string
    {
        return $this->SMTPSecure;
    }

    public function setSMTPSecure(string $SMTPSecure): Server
    {
        $this->SMTPSecure = $SMTPSecure;
        return $this;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function setPort(int $port): Server
    {
        $this->port = $port;
        return $this;
    }
}