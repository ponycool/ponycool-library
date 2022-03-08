<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/7/3
 * Time: 3:38 下午
 */

declare(strict_types=1);

namespace PonyCool\Core\SnowFlake;

use Exception;

class SnowFlake
{
    // 初始时间戳，时间起始标记点，作为基准，一般取系统的最近时间（一旦确定不能变动）
    const TWEPOCH = 1593763886000;
    // 机器标识位数
    const WORKER_ID_BITS = 5;
    // 数据中心标识位数
    const DATACENTER_ID_BITS = 5;
    // 毫秒内自增位
    const SEQUENCE_BITS = 11;

    // 工作机器ID(0~31)
    private int $workerId;
    // 数据中心ID(0~31)
    private int $datacenterId;
    // 毫秒内序列(0~4095)
    private int $sequence;

    // 机器ID最大值31
    private int $maxWorkerId = -1 ^ (-1 << self::WORKER_ID_BITS);
    // 数据中心ID最大值31
    private int $maxDatacenterId = -1 ^ (-1 << self::DATACENTER_ID_BITS);
    // 机器ID偏左移11位
    private int $workerIdShift = self::SEQUENCE_BITS;
    // 数据中心ID左移16位
    private int $datacenterIdShift = self::SEQUENCE_BITS + self::WORKER_ID_BITS;
    // 时间毫秒左移21位
    private int $timestampLeftShift = self::SEQUENCE_BITS + self::WORKER_ID_BITS + self::DATACENTER_ID_BITS;
    // 生成序列的掩码4095
    private int $sequenceMask = -1 ^ (-1 << self::SEQUENCE_BITS);
    // 上次生产id时间戳
    private int $lastTimestamp = -1;

    /**
     * @throws Exception
     */
    public function __construct(int $workerId, int $datacenterId, int $sequence = 0)
    {
        if ($workerId > $this->maxWorkerId || $workerId < 0) {
            throw new Exception("worker Id can't be greater than {$this->maxWorkerId} or less than 0");
        }

        if ($datacenterId > $this->maxDatacenterId || $datacenterId < 0) {
            throw new Exception("datacenter Id can't be greater than {$this->maxDatacenterId} or less than 0");
        }

        $this->workerId = $workerId;
        $this->datacenterId = $datacenterId;
        $this->sequence = $sequence;
    }

    /**
     * 下一个ID生成算法
     * @return string
     * @throws Exception
     */
    public function nextId(): string
    {
        $timestamp = $this->timeGen();

        if ($timestamp < $this->lastTimestamp) {
            $diffTimestamp = bcsub((string)$this->lastTimestamp, (string)$timestamp);
            throw new Exception("Clock moved backwards.  Refusing to generate id for {$diffTimestamp} milliseconds");
        }

        if ($this->lastTimestamp == $timestamp) {
            $this->sequence = ($this->sequence + 1) & $this->sequenceMask;

            if (0 == $this->sequence) {
                $timestamp = $this->tilNextMillis($this->lastTimestamp);
            }
        } else {
            $this->sequence = 0;
        }

        $this->lastTimestamp = (int)$timestamp;

        $gmpTimestamp = gmp_init($this->leftShift(bcsub((string)$timestamp, (string)self::TWEPOCH), (string)$this->timestampLeftShift));
        $gmpDatacenterId = gmp_init($this->leftShift((string)$this->datacenterId, (string)$this->datacenterIdShift));
        $gmpWorkerId = gmp_init($this->leftShift((string)$this->workerId, (string)$this->workerIdShift));
        $gmpSequence = gmp_init($this->sequence);
        return gmp_strval(gmp_or(gmp_or(gmp_or($gmpTimestamp, $gmpDatacenterId), $gmpWorkerId), $gmpSequence));
    }

    /**
     * 获取时间戳，并与上次时间戳比较
     * @param float $lastTimestamp
     * @return float
     */
    protected function tilNextMillis(float $lastTimestamp): float
    {
        $timestamp = $this->timeGen();
        while ($timestamp <= $lastTimestamp) {
            $timestamp = $this->timeGen();
        }

        return $timestamp;
    }

    /**
     * 获取系统时间戳
     * @return float
     */
    protected function timeGen(): float
    {
        return floor(microtime(true) * 1000);
    }

    /**
     * 左移 <<
     * @param string $a
     * @param string $b
     * @return string
     */
    protected function leftShift(string $a, string $b): string
    {
        return bcmul($a, bcpow(strval(2), $b));
    }
}