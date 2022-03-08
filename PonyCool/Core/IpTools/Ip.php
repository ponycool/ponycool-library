<?php
declare(strict_types=1);

namespace PonyCool\Core\IpTools;

use Exception;

class Ip
{
    use PropertyTrait;
    const IP_V4 = 'IPv4';
    const IP_V6 = 'IPv6';
    const IP_V4_MAX_PREFIX_LENGTH = 32;
    const IP_V6_MAX_PREFIX_LENGTH = 128;
    const IP_V4_OCTETS = 4;
    const IP_V6_OCTETS = 16;
    private $in_addr;

    public function __construct($ip)
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new Exception("IP地址格式无效");
        }
        $this->in_addr = inet_pton($ip);
    }

    public function __toString()
    {
        return inet_ntop($this->in_addr);
    }

    /**
     * 解析整数、二进制、十六进制IP
     * @param string $ip
     * @return Ip
     * @throws Exception
     */
    public static function parse(string $ip): Ip
    {
        if (strpos($ip, '0x') === 0) {
            $ip = substr($ip, 2);
            return self::parseHex($ip);
        }
        if (strpos($ip, '0b') === 0) {
            $ip = substr($ip, 2);
            return self::parseBin($ip);
        }
        if (is_numeric($ip)) {
            return self::parseLong((int)$ip);
        }
        return new self($ip);
    }

    /**
     * 解析二进制IP
     * @param $binIp
     * @return Ip
     * @throws Exception
     */
    public static function parseBin(string $binIp): Ip
    {
        if (!preg_match('/^([0-1]{32}|[0-1]{128})$/', $binIp)) {
            throw new Exception("Invalid binary IP address format");
        }
        $in_addr = '';
        foreach (array_map('bindec', str_split($binIp, 8)) as $char) {
            $in_addr .= pack('C*', $char);
        }
        return new self(inet_ntop($in_addr));
    }

    /**
     * 解析十六进制IP
     * @param string $hexIp
     * @return Ip
     * @throws Exception
     */
    public static function parseHex(string $hexIp): Ip
    {
        if (!preg_match('/^([0-9a-fA-F]{8}|[0-9a-fA-F]{32})$/', $hexIp)) {
            throw new Exception("Invalid hexadecimal IP address format");
        }
        return new self(inet_ntop(pack('H*', $hexIp)));
    }

    /**
     * 解析整数IP
     * @param int $longIp
     * @param string $version
     * @return Ip
     * @throws Exception
     */
    public static function parseLong(int $longIp, string $version = self::IP_V4)
    {
        if ($version === self::IP_V4) {
            $ip = new self(long2ip($longIp));
        } else {
            $binary = array();
            for ($i = 0; $i < self::IP_V6_OCTETS; $i++) {
                $binary[] = bcmod($longIp, 256);
                $longIp = bcdiv($longIp, 256, 0);
            }
            $ip = new self(inet_ntop(call_user_func_array('pack', array_merge(array('C*'), array_reverse($binary)))));
        }
        return $ip;
    }

    public static function parseInAddr($inAddr): Ip
    {
        return new self(inet_ntop($inAddr));
    }

    /**
     * 获取IP版本
     * @return string
     */
    public function getVersion(): string
    {
        $version = '';
        if (filter_var(inet_ntop($this->in_addr), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $version = self::IP_V4;
        } elseif (filter_var(inet_ntop($this->in_addr), FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $version = self::IP_V6;
        }
        return $version;
    }

    /**
     * 获取IP最大长度
     * @return int
     */
    public function getMaxPrefixLength(): int
    {
        return $this->getVersion() === self::IP_V4
            ? self::IP_V4_MAX_PREFIX_LENGTH
            : self::IP_V6_MAX_PREFIX_LENGTH;
    }

    public function getOctetsCount(): int
    {
        return $this->getVersion() === self::IP_V4
            ? self::IP_V4_OCTETS
            : self::IP_V6_OCTETS;
    }

    public function getReversePointer(): string
    {
        if ($this->getVersion() === self::IP_V4) {
            $reverseOctets = array_reverse(explode('.', $this->__toString()));
            $reversePointer = implode('.', $reverseOctets) . '.in-addr.arpa';
        } else {
            $unpacked = unpack('H*hex', $this->in_addr);
            $reverseOctets = array_reverse(str_split($unpacked['hex']));
            $reversePointer = implode('.', $reverseOctets) . '.ip6.arpa';
        }
        return $reversePointer;
    }

    public function inAddr()
    {
        return $this->in_addr;
    }

    /**
     * 转换IP为二进制形式
     * @return string
     */
    public function toBin(): string
    {
        $binary = array();
        foreach (unpack('C*', $this->in_addr) as $char) {
            $binary[] = str_pad(decbin($char), 8, '0', STR_PAD_LEFT);
        }
        return implode($binary);
    }

    /**
     * 转换IP为十六进制形式
     * @return string
     */
    public function toHex(): string
    {
        return bin2hex($this->in_addr);
    }

    /**
     * 转换IP为整数形式
     * @return string
     */
    public function toLong(): string
    {
        $long = 0;
        if ($this->getVersion() === self::IP_V4) {
            $long = sprintf('%u', ip2long(inet_ntop($this->in_addr)));
        } else {
            $octet = self::IP_V6_OCTETS - 1;
            foreach ($chars = unpack('C*', $this->in_addr) as $char) {
                $long = bcadd($long, bcmul($char, bcpow(256, $octet--)));
            }
        }
        return $long;
    }

    public function next($to = 1): Ip
    {
        if ($to < 0) {
            throw new Exception("Number must be greater than 0");
        }
        $unpacked = unpack('C*', $this->in_addr);
        for ($i = 0; $i < $to; $i++) {
            for ($byte = count($unpacked); $byte >= 0; --$byte) {
                if ($unpacked[$byte] < 255) {
                    $unpacked[$byte]++;
                    break;
                }
                $unpacked[$byte] = 0;
            }
        }
        return new self(inet_ntop(call_user_func_array('pack', array_merge(array('C*'), $unpacked))));
    }

    public function prev($to = 1): Ip
    {
        if ($to < 0) {
            throw new Exception("Number must be greater than 0");
        }
        $unpacked = unpack('C*', $this->in_addr);
        for ($i = 0; $i < $to; $i++) {
            for ($byte = count($unpacked); $byte >= 0; --$byte) {
                if ($unpacked[$byte] === 0) {
                    $unpacked[$byte] = 255;
                } else {
                    $unpacked[$byte]--;
                    break;
                }
            }
        }
        return new self(inet_ntop(call_user_func_array('pack', array_merge(array('C*'), $unpacked))));
    }

}