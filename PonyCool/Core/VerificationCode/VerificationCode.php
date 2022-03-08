<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/7/6
 * Time: 10:40 上午
 */
declare(strict_types=1);

namespace PonyCool\Core\VerificationCode;

class VerificationCode
{
    protected int $length;

    public function __construct()
    {
        $this->length = 4;
    }

    /**
     * @return int
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * @param int $length
     */
    public function setLength(int $length): void
    {
        $this->length = $length;
    }

    /**
     * 生成验证码
     * @return string
     */
    public function generate(): string
    {
        $code = '';
        $len = $this->getLength();
        for ($i = 0; $i < $len; $i++) {
            $code .= chr(mt_rand(48, 57));
        }
        return $code;
    }
}