<?php
declare(strict_types=1);

namespace PonyCool\Core\Jwt\Validation\Strategy;

interface StrategyInterface
{
    public function validator(string $param): bool;
}
