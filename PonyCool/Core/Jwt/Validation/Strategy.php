<?php
declare(strict_types=1);

namespace PonyCool\Core\Jwt\Validation;

abstract class Strategy
{
    // 策略
    protected string $strategy;
    // 验证策略
    protected object $validationStrategy;

    abstract public function validator(string $param): bool;
}
