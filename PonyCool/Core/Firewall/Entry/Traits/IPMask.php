<?php
declare(strict_types=1);

namespace PonyCool\Core\Firewall\Entry\Traits;


trait IPMask
{
    public static function getMatchRegex(): string
    {
        $pRegex = IPRange::getMatchRegex();
        $regex = substr($pRegex, 2, (strlen($pRegex) - 4));
        $separator = static::$separatorRegex;
        $maskRegex = static::getMaskRegex();

        return sprintf("/^%s%s%s$/", $regex, $separator, $maskRegex);
    }

    public static function getMaskRegex(): string
    {
        return IPRange::getMatchRegex();
    }

    public function getRange(bool $long = true): array
    {
        $parts = $this->getParts();
        $ret = array();

        $ipLong = $this->ip2long($parts['ip']);
        $maskLong = $this->ip2long($parts['mask']);

        $ret['begin'] = $this->IPLongAnd(
            $ipLong,
            $maskLong
        );

        $ret['end'] = $this->IPLongOr(
            $ipLong,
            $this->IPLongCom($maskLong)
        );

        if ($parts['mask'] != "255.255.255.255") {
            $ret['begin'] = $this->IPLongAdd($ret['begin'], (string)1);
        }

        $ret['begin'] = $this->long2ip($ret['begin']);
        $ret['end'] = $this->long2ip($ret['end']);

        if ($long) {
            $ret['begin'] = $this->ip2long($ret['begin']);
            $ret['end'] = $this->ip2long($ret['end']);
        }

        return $ret;
    }

    public function getParts(): array
    {
        $keys = array('ip', 'mask');

        return array_combine($keys, preg_split('/' . self::$separatorRegex . '/', $this->template));
    }
}