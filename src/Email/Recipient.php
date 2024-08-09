<?php
/**
 * Created By PhpStorm
 * User: Pony
 * Data: 2024/8/8
 * Time: 下午5:12
 */
declare(strict_types=1);

namespace PonyCool\Email;

class Recipient
{
    // 收件人邮箱
    protected string $email;
    // 收件人姓名
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

    public function setEmail(string $email): Recipient
    {
        $this->email = $email;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Recipient
    {
        $this->name = $name;
        return $this;
    }

}