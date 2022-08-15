<?php
declare(strict_types=1);

namespace PonyCool\Core\Firewall\Entry\Traits;


trait IPRange
{
    public static function match(string $entry): bool
    {
        $entries = preg_split('/' . static::$separatorRegex . '/', $entry);

        if (count($entries) == 2) {
            foreach ($entries as $ent) {
                if (!static::matchIp(trim($ent))) {
                    return false;
                }
            }
        } else {
            return false;
        }

        return true;
    }

    public function check(string $entry): bool
    {
        if (!self::matchIp($entry)) {
            return false;
        }

        $entryLong = $this->ip2long($entry);

        $range = $this->getRange();

        return $this->IPLongCompare($range['begin'], $entryLong, '<=') && $this->IPLongCompare($range['end'], $entryLong, '>=');
    }

    protected function getRange(bool $long = true): array
    {
        $parts = $this->getParts();
        $keys = array('begin', 'end');

        $parts['ip_start'] = $this->ip2long($parts['ip_start']);
        $parts['ip_end'] = $this->ip2long($parts['ip_end']);

        natsort($parts);

        $parts = array_combine($keys, array_values($parts));

        if (!$long) {
            $parts['begin'] = $this->long2ip($parts['begin']);
            $parts['end'] = $this->long2ip((string)$parts['end']);
        }

        return $parts;
    }

    public function getParts(): array
    {
        $keys = array('ip_start', 'ip_end');

        return array_combine($keys, preg_split('/' . self::$separatorRegex . '/', $this->template));
    }

    public function getMatchingEntries(): array
    {
        $limits = $this->getRange();
        $current = $limits['begin'];
        $entries[] = $this->long2ip($current);
        $entries = array();

        while ($current != $limits['end']) {
            $current = $this->IpLongAdd($current, "1");
            $entries[] = $this->long2ip($current);
        }

        return $entries;
    }
}