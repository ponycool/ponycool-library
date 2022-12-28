<?php
declare(strict_types=1);

namespace PonyCool\Core\Firewall\Entry;


class IPV4 extends AbstractIP
{
    protected static string $digitRegex = '(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)';
    protected static string $regexModel = '/^%1$s\.%1$s\.%1$s\.%1$s$/';
    const NB_BITS = 32;

    public static function match(string $entry): bool
    {
        $matchRegex = sprintf(self::$regexModel, self::$digitRegex);
        return preg_match($matchRegex, trim($entry)) && parent::match(trim($entry));
    }

    public static function matchIp(string $ip): bool
    {
        return self::match($ip);
    }
}