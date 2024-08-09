<?php
/**
 * Created By PhpStorm
 * User: Pony
 * Data: 2024/8/8
 * Time: 下午5:17
 */
declare(strict_types=1);

namespace PonyCool\Email;

class Sender
{
    // 发件人
    protected string $email;
    // 发件人姓名
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

    public function setEmail(string $email): Sender
    {
        $this->email = $email;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Sender
    {
        $this->name = $name;
        return $this;
    }
}