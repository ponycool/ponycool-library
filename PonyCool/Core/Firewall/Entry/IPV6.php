<?php


namespace PonyCool\Core\Firewall\Entry;


class IPV6 extends AbstractIP
{
    public static string $digitRegex = '([0-9A-Fa-f]{1,4})';

    const NB_BITS = 128;

    public static function match(string $entry): bool
    {
        return !IPV4::match(trim($entry)) && AbstractIP::match(trim($entry));
    }

    public function check(string $entry): bool
    {
        if (!IPV6::matchIp($entry)) {
            return false;
        }

        $entryLong = $this->ip2long($entry);
        $templateLong = $this->ip2long($this->template);

        return $this->IPLongCompare($entryLong, $templateLong, '=');
    }

    public static function getFreeDigit(string $entry): string
    {
        do {
            $digit = dechex(rand(0, 65535));
        } while (strstr($entry, $digit));

        return $digit;
    }

    public static function matchIp(string $ip): bool
    {
        return IPV6::match($ip);
    }
}
