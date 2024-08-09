<?php
/**
 * Created By PhpStorm
 * User: Pony
 * Data: 2024/8/8
 * Time: 下午5:49
 */
declare(strict_types=1);

namespace PonyCool\Email;

class ReplyTo
{
    // 回复邮箱
    protected string $email;
    protected string $name;

    public function __construct(string $email, string $name = '')
    {
        $this->email = $email;
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): ReplyTo
    {
        $this->email = $email;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): ReplyTo
    {
        $this->name = $name;
        return $this;
    }
}