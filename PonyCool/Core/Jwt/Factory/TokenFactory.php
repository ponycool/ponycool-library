<?php
declare(strict_types=1);

namespace PonyCool\Core\Jwt\Factory;

abstract class TokenFactory
{
    abstract public function createToken();
}
