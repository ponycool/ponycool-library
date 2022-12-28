<?php
declare(strict_types=1);

namespace PonyCool\Core\Firewall\Entry;


class IPV4Wildcard extends IPV4Range
{
    protected static string $digitRegex = '((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)|\*)';

    public function check(string $entry): bool
    {
        return ((new IPV4Range)->check($entry) && AbstractIP::check($entry));
    }

    protected function getCheckRegex(): string
    {
        $regex = $this->template;

        $patterns = array(
            '.',
            '*',
        );
        $replaces = array(
            '\.',
            parent::$digitRegex,
        );

        return sprintf('/^%s$/', str_replace($patterns, $replaces, $regex));
    }

    public static function match(string $entry): bool
    {
        if (!strpos($entry, '*')) {
            return false;
        }

        $entry = str_replace('*', '12', $entry);

        return IPV4::match(trim($entry));
    }

    public function getParts(): array
    {
        return array(
            'ip_start' => str_replace('*', '0', $this->template),
            'ip_end' => str_replace('*', '255', $this->template)
        );
    }
}