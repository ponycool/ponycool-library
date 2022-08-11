<?php
declare(strict_types=1);

namespace PonyCool\Core\Firewall\Entry;


abstract class AbstractIP extends AbstractEntry
{
    public static function match(string $entry): bool
    {
        return (bool)@inet_pton(trim($entry));
    }

    public function check(string $entry): bool
    {
        return (bool)preg_match(
            $this->getCheckRegex(),
            $this->getEntryFull($entry)
        );
    }

    /**
     * 将短IP转换为完整IP
     * @param string $entry
     * @return string
     */
    public function getEntryFull(string $entry): string
    {
        return $this->long2ip($this->ip2long($entry), false);
    }

    /**
     * 获取模板的正则表达式
     * @return string
     */
    protected function getCheckRegex(): string
    {
        $regex = $this->template;

        return sprintf('/^%s$/', str_replace('.', '\.', $regex));
    }

    /**
     * 将IP转换成长整型数字（IPV6）
     * @param string $long
     * @return string
     */
    protected function ip2long(string $long): string
    {
        switch (static::NB_BITS) {
            case 128:
                return $this->ip2long6($long);
            default:
                return sprintf('%u', ip2long($long));
        }
    }

    /**
     * 将IPV6转换成长整型数字
     * @param string $ipv6
     * @return string
     */
    protected function ip2long6(string $ipv6): string
    {
        $ipN = inet_pton($ipv6);
        $byte = 0;
        $ipv6Long = 0;

        while ($byte < 16) {
            $ipv6Long = bcadd(bcmul($ipv6Long, "256"), (string)ord($ipN[$byte]));
            $byte++;
        }
        return $ipv6Long;
    }

    /**
     * 将长整型转化为字符串形式带点的互联网标准格式地址（IPV4）
     * @param string $long
     * @param bool $abbr
     * @return string
     */
    protected function long2ip(string $long, bool $abbr = true): string
    {
        return match (static::NB_BITS) {
            128 => $this->long2ip6($long, $abbr),
            default => strval(long2ip((int)$long)),
        };
    }

    /**
     * 将长整型转化为字符串形式带点的互联网标准格式地址（IPV6）
     * @param string $ipv6long
     * @param bool $abbr
     * @return string
     */
    protected function long2ip6(string $ipv6long, bool $abbr = true): string
    {
        $ipv6Arr = array();

        for ($part = 0; $part <= 7; $part++) {
            $hexPart = dechex((int)bcmod($ipv6long, "65536"));
            $ipv6long = bcdiv($ipv6long, "65536", 0);
            $hexFullPart = str_pad($hexPart, 4, "0", STR_PAD_LEFT);
            $ipv6Arr[] = $hexFullPart;
        }

        $ipv6 = implode(':', array_reverse($ipv6Arr));

        if ($abbr) {
            $ipv6 = inet_ntop(inet_pton($ipv6));
        }

        return $ipv6;
    }

    /**
     * 对比两个IP
     * @param string $long1
     * @param string $long2
     * @param string $operator
     * @return bool
     */
    protected function IPLongCompare(string $long1, string $long2, string $operator = '='): bool
    {
        $operators = preg_split('//', $operator);
        $diff = bccomp($long1, $long2);

        foreach ($operators as $operator) {
            switch (true) {
                case (($operator === '=') && ($diff == 0)):
                case (($operator === '<') && ($diff < 0)):
                case (($operator === '>') && ($diff > 0)):
                    return true;
            }
        }

        return false;
    }

    /**
     * @param string $long1
     * @param string $long2
     * @return string
     */
    protected function IPLongAnd(string $long1, string $long2): string
    {
        // The biggest power of 2 lowest than PHP_INT_MAX
        // PHP_INT_MAX == 2 ** (PHP_INT_SIZE * 8 - 1) - 1
        $divisor = 1 << (PHP_INT_SIZE * 8 - 2);
        $result = '0';
        $i = 0;

        // As soon as a number is 0, the result of a bitwise-& cannot change.
        while ($long1 && $long2) {
            // Keep last bits og longs*
            $chunk1 = bcmod($long1, (string)$divisor);
            $chunk2 = bcmod($long2, (string)$divisor);
            // Remove last bits of longs*
            $long1 = bcdiv($long1, (string)$divisor, 0);
            $long2 = bcdiv($long2, (string)$divisor, 0);

            // Compare last bits
            $chunkResult = (int)$chunk1 & (int)$chunk2;

            // Add last bits comparison to global result
            $result = bcadd($result, bcmul((string)$chunkResult, bcpow((string)$divisor, (string)$i++)));
        }

        return $result;
    }

    /**
     * @param string $long1
     * @param string $long2
     * @return string
     */
    protected function IPLongOr(string $long1, string $long2): string
    {
        // The biggest power of 2 lowest than PHP_INT_MAX
        // PHP_INT_MAX == 2 ** (PHP_INT_SIZE * 8 - 1) - 1
        $divisor = 1 << (PHP_INT_SIZE * 8 - 2);
        $result = '0';
        $i = 0;

        // Stop only when numbers have been completely treated
        while ($long1 || $long2) {
            // Keep last bits og longs*
            $chunk1 = bcmod($long1, (string)$divisor);
            $chunk2 = bcmod($long2, (string)$divisor);
            // Remove last bits of longs*
            $long1 = bcdiv($long1, (string)$divisor, 0);
            $long2 = bcdiv($long2, (string)$divisor, 0);

            // Compare last bits
            $chunkResult = (int)$chunk1 | (int)$chunk2;

            // Add last bits comparison to global result
            $result = bcadd($result, bcmul((string)$chunkResult, bcpow((string)$divisor, (string)$i++)));
        }

        return $result;
    }

    /**
     * @param string $long1
     * @param string $long2
     * @return string
     */
    protected function IPLongAdd(string $long1, string $long2): string
    {
        return bcadd($long1, $long2);
    }

    /**
     * @param string $long
     * @return string
     */
    protected function IPLongCom(string $long): string
    {
        return bcsub(bcpow("2", (string)static::NB_BITS), bcadd($long, "1"));
    }

    /**
     * @param string $long
     * @param int $fromBase
     * @param int $toBase
     * @return string
     */
    protected function IPLongBaseConvert(string $long, int $fromBase = 10, int $toBase = 36): string
    {
        $str = trim($long);
        if (intval($fromBase) != 10) {
            $len = strlen($str);
            $q = 0;
            for ($i = 0; $i < $len; $i++) {
                $r = base_convert($str[$i], $fromBase, 10);
                $q = bcadd(bcmul((string)$q, (string)$fromBase), $r);
            }
        } else {
            $q = $str;
        }

        if (intval($toBase) != 10) {
            $s = '';
            while (bccomp($q, '0', 0) > 0) {
                $r = intval(bcmod($q, (string)$toBase));
                $s = base_convert((string)$r, 10, $toBase) . $s;
                $q = bcdiv($q, (string)$toBase, 0);
            }
        } else {
            $s = $q;
        }

        return $s;
    }

    /**
     * @return array
     */
    public function getMatchingEntries(): array
    {
        return array($this->template);
    }
}