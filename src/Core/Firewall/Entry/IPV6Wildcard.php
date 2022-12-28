<?php
declare(strict_types=1);

namespace PonyCool\Core\Firewall\Entry;


class IPV6Wildcard extends IPV6Range
{
    public static string $digitRegex = '(([0-9A-Fa-f]{1,4})|\*)';

    protected string $freeDigit;

    public function check(string $entry): bool
    {
        return ((new IPV6Range)->check($entry) && AbstractIP::check($entry));
    }

    protected function getCheckRegex(): string
    {
        $digit = IPV6::getFreeDigit($this->template);
        $templateFull = str_replace(
            $digit,
            '*',
            $this->long2ip(
                $this->ip2long(
                    str_replace('*', $digit, $this->template)
                ),
                false
            )
        );

        return '/^' . str_replace('*', IPV6::$digitRegex, $templateFull) . '$/';
    }

    public static function match(string $entry): bool
    {
        if (!strpos($entry, '*')) {
            return false;
        }

        $rpDigit = IPV6::getFreeDigit($entry);
        $entry = str_replace('*', $rpDigit, $entry);

        return IPV6::match(trim($entry));
    }

    public function getParts(): array
    {
        return array(
            'ip_start' => str_replace('*', '0000', $this->template),
            'ip_end' => str_replace('*', 'ffff', $this->template)
        );
    }
}
