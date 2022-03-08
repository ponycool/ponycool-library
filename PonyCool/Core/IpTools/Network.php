<?php

namespace PonyCool\Core\IpTools;

use Iterator,
    Countable,
    Exception;

class Network implements Iterator, Countable
{
    use PropertyTrait;
    private $ip;
    private $netmask;
    private $position = 0;

    public function __construct(Ip $ip, Ip $netmask)
    {
        $this->setIp($ip);
        $this->setNetmask($netmask);
    }

    public function __toString(): string
    {
        return $this->getCIDR();
    }

    public static function parse($data): Network
    {
        if (preg_match('~^(.+?)/(\d+)$~', $data, $matches)) {
            $ip = Ip::parse($matches[1]);
            $netmask = self::prefix2netmask((int)$matches[2], $ip->getVersion());
        } elseif (strpos($data, ' ')) {
            list($ip, $netmask) = explode(' ', $data, 2);
            $ip = Ip::parse($ip);
            $netmask = Ip::parse($netmask);
        } else {
            $ip = Ip::parse($data);
            $netmask = self::prefix2netmask($ip->getMaxPrefixLength(), $ip->getVersion());
        }
        return new self($ip, $netmask);
    }

    public static function prefix2netmask($prefixLength, $version): Ip
    {
        if (!in_array($version, array(Ip::IP_V4, Ip::IP_V6))) {
            throw new Exception("Wrong IP version");
        }
        $maxPrefixLength = $version === Ip::IP_V4
            ? Ip::IP_V4_MAX_PREFIX_LENGTH
            : Ip::IP_V6_MAX_PREFIX_LENGTH;
        if (!is_numeric($prefixLength)
            || !($prefixLength >= 0 && $prefixLength <= $maxPrefixLength)
        ) {
            throw new Exception('Invalid prefix length');
        }
        $binIp = str_pad(str_pad('', (int)$prefixLength, '1'), $maxPrefixLength, '0');
        return Ip::parseBin($binIp);
    }

    public static function netmask2prefix(Ip $ip): int
    {
        return strlen(rtrim($ip->toBin(), "0"));
    }

    public function setIp(Ip $ip): void
    {
        if (isset($this->netmask) && $this->netmask->getVersion() !== $ip->getVersion()) {
            throw new Exception('IP version is not same as Netmask version');
        }
        $this->ip = $ip;
    }

    /**
     * 设置子网掩码
     * @param Ip $ip
     * @throws Exception
     */
    public function setNetmask(Ip $ip): void
    {
        if (!preg_match('/^1*0*$/', $ip->toBin())) {
            throw new Exception('Invalid Netmask address format');
        }
        if (isset($this->ip) && $ip->getVersion() !== $this->ip->getVersion()) {
            throw new Exception('Netmask version is not same as IP version');
        }
        $this->netmask = $ip;
    }

    public function setPrefixLength($prefixLength)
    {
        $this->setNetmask(self::prefix2netmask((int)$prefixLength, $this->ip->getVersion()));
    }

    public function getIp(): Ip
    {
        return $this->ip;
    }

    public function getNetmask(): Ip
    {
        return $this->netmask;
    }

    public function getNetwork(): Ip
    {
        return new Ip(inet_ntop($this->getIp()->inAddr() & $this->getNetmask()->inAddr()));
    }

    public function getPrefixLength(): int
    {
        return self::netmask2prefix($this->getNetmask());
    }

    public function getCIDR(): string
    {
        return sprintf('%s/%s', $this->getNetwork(), $this->getPrefixLength());
    }

    public function getWildcard(): Ip
    {
        return new Ip(inet_ntop(~$this->getNetmask()->inAddr()));
    }

    public function getBroadcast(): Ip
    {
        return new Ip(inet_ntop($this->getNetwork()->inAddr() | ~$this->getNetmask()->inAddr()));
    }

    public function getFirstIp(): Ip
    {
        return $this->getNetwork();
    }

    public function getLastIp(): Ip
    {
        return $this->getBroadcast();
    }

    public function getBlockSize()
    {
        $maxPrefixLength = $this->ip->getMaxPrefixLength();
        $prefixLength = $this->getPrefixLength();
        if ($this->ip->getVersion() === Ip::IP_V6) {
            return bcpow('2', (string)($maxPrefixLength - $prefixLength));
        }
        return pow(2, $maxPrefixLength - $prefixLength);
    }

    public function getHosts(): Range
    {
        $firstHost = $this->getNetwork();
        $lastHost = $this->getBroadcast();
        if ($this->ip->getVersion() === Ip::IP_V4) {
            if ($this->getBlockSize() > 2) {
                $firstHost = Ip::parseBin(substr($firstHost->toBin(), 0, $firstHost->getMaxPrefixLength() - 1) . '1');
                $lastHost = Ip::parseBin(substr($lastHost->toBin(), 0, $lastHost->getMaxPrefixLength() - 1) . '0');
            }
        }
        return new Range($firstHost, $lastHost);
    }

    public function exclude($exclude): array
    {
        $exclude = self::parse($exclude);
        if (strcmp($exclude->getFirstIp()->inAddr(), $this->getLastIp()->inAddr()) > 0
            || strcmp($exclude->getLastIp()->inAddr(), $this->getFirstIp()->inAddr()) < 0
        ) {
            throw new Exception('Exclude subnet not within target network');
        }
        $networks = array($exclude);
        $newPrefixLength = $this->getPrefixLength() + 1;
        if ($newPrefixLength > $this->ip->getMaxPrefixLength()) {
            return $networks;
        }
        $lower = clone $this;
        $lower->setPrefixLength($newPrefixLength);
        $upper = clone $lower;
        $upper->setIp($lower->getLastIp()->next());
        while ($newPrefixLength <= $exclude->getPrefixLength()) {
            $range = new Range($lower->getFirstIp(), $lower->getLastIp());
            if ($range->contains($exclude)) {
                $matched = $lower;
                $unmatched = $upper;
            } else {
                $matched = $upper;
                $unmatched = $lower;
            }
            $networks[] = clone $unmatched;
            if (++$newPrefixLength > $this->getNetwork()->getMaxPrefixLength()) break;
            $matched->setPrefixLength($newPrefixLength);
            $unmatched->setPrefixLength($newPrefixLength);
            $unmatched->setIp($matched->getLastIp()->next());
        }
        sort($networks);
        return $networks;
    }

    public function moveTo($prefixLength): array
    {
        $maxPrefixLength = $this->ip->getMaxPrefixLength();
        if ($prefixLength <= $this->getPrefixLength() || $prefixLength > $maxPrefixLength) {
            throw new \Exception('Invalid prefix length ');
        }
        $netmask = self::prefix2netmask($prefixLength, $this->ip->getVersion());
        $networks = array();
        $subnet = clone $this;
        $subnet->setPrefixLength($prefixLength);
        while ($subnet->ip->inAddr() <= $this->getLastIp()->inAddr()) {
            $networks[] = $subnet;
            $subnet = new self($subnet->getLastIp()->next(), $netmask);
        }
        return $networks;
    }

    public function current(): Ip
    {
        return $this->getFirstIp()->next($this->position);
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return strcmp($this->getFirstIp()->next($this->position)->inAddr(), $this->getLastIp()->inAddr()) <= 0;
    }

    public function count(): int
    {
        return (integer)$this->getBlockSize();
    }
}