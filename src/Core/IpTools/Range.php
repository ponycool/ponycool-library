<?php

namespace PonyCool\Core\IpTools;

use Iterator,
    Countable;

class Range implements Iterator, Countable
{
    use PropertyTrait;
    private $firstIp;
    private $lastIp;
    private $position = 0;

    public function __construct(Ip $firstIp, Ip $lastIp)
    {
        $this->setFirstIp($firstIp);
        $this->setLastIp($lastIp);
    }

    public static function parse($data): Range
    {
        if (strpos($data, '/') || strpos($data, ' ')) {
            $network = Network::parse($data);
            $firstIp = $network->getFirstIp();
            $lastIp = $network->getLastIp();
        } elseif (strpos($data, '*') !== false) {
            $firstIp = Ip::parse(str_replace('*', '0', $data));
            $lastIp = Ip::parse(str_replace('*', '255', $data));
        } elseif (strpos($data, '-')) {
            list($first, $last) = explode('-', $data, 2);
            $firstIp = Ip::parse($first);
            $lastIp = Ip::parse($last);
        } else {
            $firstIp = Ip::parse($data);
            $lastIp = clone $firstIp;
        }
        return new self($firstIp, $lastIp);
    }

    public function contains($find)
    {
        if ($find instanceof Ip) {
            $within = (strcmp($find->inAddr(), $this->firstIp->inAddr()) >= 0)
                && (strcmp($find->inAddr(), $this->lastIp->inAddr()) <= 0);
        } elseif ($find instanceof Range || $find instanceof Network) {
            /**
             * @var Network|Range $find
             */
            $within = (strcmp($find->getFirstIp()->inAddr(), $this->firstIp->inAddr()) >= 0)
                && (strcmp($find->getLastIp()->inAddr(), $this->lastIp->inAddr()) <= 0);
        } else {
            throw new Exception('Invalid type');
        }
        return $within;
    }

    public function setFirstIp(Ip $ip)
    {
        if ($this->lastIp && strcmp($ip->inAddr(), $this->lastIp->inAddr()) > 0) {
            throw new Exception('First IP is grater than second');
        }
        $this->firstIp = $ip;
    }

    public function setLastIp(Ip $ip)
    {
        if ($this->firstIp && strcmp($ip->inAddr(), $this->firstIp->inAddr()) < 0) {
            throw new Exception('Last IP is less than first');
        }
        $this->lastIp = $ip;
    }

    public function getFirstIp(): Ip
    {
        return $this->firstIp;
    }

    public function getLastIp(): Ip
    {
        return $this->lastIp;
    }

    public function getNetworks(): array
    {
        $span = $this->getSpanNetwork();
        $networks = array();
        if ($span->getFirstIp()->inAddr() === $this->firstIp->inAddr()
            && $span->getLastIp()->inAddr() === $this->lastIp->inAddr()
        ) {
            $networks = array($span);
        } else {
            if ($span->getFirstIp()->inAddr() !== $this->firstIp->inAddr()) {
                $excluded = $span->exclude($this->firstIp->prev());
                foreach ($excluded as $network) {
                    if (strcmp($network->getFirstIp()->inAddr(), $this->firstIp->inAddr()) >= 0) {
                        $networks[] = $network;
                    }
                }
            }
            if ($span->getLastIp()->inAddr() !== $this->lastIp->inAddr()) {
                if (!$networks) {
                    $excluded = $span->exclude($this->lastIp->next());
                } else {
                    $excluded = array_pop($networks);
                    $excluded = $excluded->exclude($this->lastIp->next());
                }
                foreach ($excluded as $network) {
                    $networks[] = $network;
                    if ($network->getLastIp()->inAddr() === $this->lastIp->inAddr()) {
                        break;
                    }
                }
            }
        }
        return $networks;
    }

    public function getSpanNetwork()
    {
        $xorIp = Ip::parseInAddr($this->getFirstIp()->inAddr() ^ $this->getLastIp()->inAddr());
        preg_match('/^(0*)/', $xorIp->toBin(), $match);
        $prefixLength = strlen($match[1]);
        $ip = Ip::parseBin(str_pad(substr($this->getFirstIp()->toBin(), 0, $prefixLength), $xorIp->getMaxPrefixLength(), '0'));
        return new Network($ip, Network::prefix2netmask($prefixLength, $ip->getVersion()));
    }

    public function current()
    {
        return $this->firstIp->next($this->position);
    }

    public function key()
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

    public function valid()
    {
        return strcmp($this->firstIp->next($this->position)->inAddr(), $this->lastIp->inAddr()) <= 0;
    }

    public function count()
    {
        return (integer)bcadd(bcsub($this->lastIp->toLong(), $this->firstIp->toLong()), 1);
    }

}