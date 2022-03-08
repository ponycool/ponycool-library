<?php
declare(strict_types=1);

namespace PonyCool\Core\Firewall\Entry\Traits;


trait IPCIDR
{
    public static function match(string $entry): bool
    {
        $entries = preg_split('/' . static::$separatorRegex . '/', $entry);

        if (count($entries) == 2) {
            $checkIp = static::matchIp($entries[0]);

            if ($checkIp && ($entries[1] >= 0) && ($entries[1] <= static::NB_BITS)) {
                return true;
            }
        }

        return false;
    }

    public function getParts(): array
    {
        $keys = array('ip', 'mask');
        $parts = array_combine($keys, preg_split('/' . self::$separatorRegex . '/', $this->template));

        $bin = str_pad(str_repeat('1', (int)$parts['mask']), self::NB_BITS, '0');

        $parts['mask'] = $this->long2ip($this->IPLongBaseConvert($bin, 2, 10));

        return $parts;
    }
}