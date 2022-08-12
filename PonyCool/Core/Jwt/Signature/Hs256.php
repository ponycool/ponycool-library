<?php
declare(strict_types=1);

namespace PonyCool\Core\Jwt\Signature;

class Hs256
{
    public function encrypt(string $secret, string $raw): string
    {
        return hash_hmac("SHA256", $raw, $secret, true);
    }

    public function decrypt()
    {
        // TODO: Implement decrypt() method.
    }
}
