<?php
declare(strict_types=1);

namespace PonyCool\Core\Jwt\Factory;

use PonyCool\Core\Jwt\Token\JwtToken;

class JwtTokenFactory extends TokenFactory
{
    public function createToken(): JwtToken
    {
        return new JwtToken();
    }
}
