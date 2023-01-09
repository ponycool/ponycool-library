<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/7/9
 * Time: 3:57 下午
 */
declare(strict_types=1);

namespace PonyCool\Redis;


interface OperationsInterface
{

    /**
     * 检测
     * @return bool
     */
    public function ping(): bool;

    /**
     * 保存键值
     * @param string $key
     * @param $data
     * @param int|null $ttl
     * @return bool
     */
    public function set(string $key, $data, ?int $ttl = null): bool;

    /**
     * 为哈希表中的字段赋值
     * @param string $key
     * @param string $hashKey
     * @param string $value
     * @return bool|int
     */
    public function hSet(string $key, string $hashKey, string $value): bool|int;

    /**
     * 同时将多个 field-value (字段-值)对设置到哈希表中
     * @param string $key
     * @param array $hashKeys
     * @return bool
     */
    public function hMSet(string $key, array $hashKeys): bool;

    /**
     * 设置 key 的过期时间
     * @param string $key
     * @param int $ttl
     * @return bool
     */
    public function expire(string $key, int $ttl): bool;

    /**
     * 设置 key 的过期时间，以毫秒为单位设置 key 的生存时间
     * @param string $key
     * @param int $ttl
     * @return bool
     */
    public function pExpire(string $key, int $ttl): bool;

    /**
     * 以 UNIX 时间戳(unix timestamp)格式设置 key 的过期时间
     * @param string $key
     * @param int $timestamp
     * @return bool
     */
    public function expireAt(string $key, int $timestamp): bool;

    /**
     * 以 UNIX 时间戳(unix timestamp)格式设置 key 的过期时间，以毫秒计
     * @param string $key
     * @param int $timestamp
     * @return bool
     */
    public function pExpireAt(string $key, int $timestamp): bool;

    /**
     * 获取指定 key 的值
     * @param string $key
     * @return mixed
     */
    public function get(string $key): mixed;

    /**
     * 获取存储在指定 key 中字符串的子字符串
     * @param string $key
     * @param int $start
     * @param int $end
     * @return string
     */
    public function getRange(string $key, int $start, int $end): string;

    /**
     * 设置指定 key 的值，并返回 key 的旧值
     * @param string $key
     * @param string $value
     * @return mixed
     */
    public function getSet(string $key, string $value): mixed;

    /**
     * 以秒为单位返回 key 的剩余过期时间
     * @param string $key
     * @return bool|int
     */
    public function getTtl(string $key): bool|int;

    /**
     * 以毫秒为单位返回 key 的剩余的过期时间
     * @param string $key
     * @return bool|int
     */
    public function getPTtl(string $key): bool|int;

    /**
     * 从当前数据库中随机返回一个 key
     * @return string
     */
    public function getRandomKey(): string;

    /**
     * 返回 key 所储存的值的类型
     * @param string $key
     * @return int
     */
    public function getType(string $key): int;

    /**
     * 查找所有符合给定模式 pattern 的 key
     * @param string $pattern
     * @return array
     */
    public function keys(string $pattern): array;

    /**
     * 返回哈希表所有的值
     * @param string $key
     * @return array
     */
    public function hVals(string $key): array;

    /**
     * 检查给定 key 是否存在
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool;

    /**
     * 查看哈希表 key 中，指定的字段是否存在
     * @param string $key
     * @param string $hashKey
     * @return bool
     */
    public function hExists(string $key, string $hashKey): bool;

    /**
     * 修改 key 的名称
     * @param string $srcKey
     * @param string $dstKey
     * @return bool
     */
    public function rename(string $srcKey, string $dstKey): bool;

    /**
     * 新的 key 不存在时修改 key 的名称
     * @param string $srcKey
     * @param string $dstKey
     * @return bool
     */
    public function renameNx(string $srcKey, string $dstKey): bool;

    /**
     * 将 key 中储存的数字值增一
     * @param string $key
     * @return int
     */
    public function incr(string $key): int;

    /**
     * 将 key 所储存的值加上给定的增量值
     * @param string $key
     * @param int $value
     * @return int
     */
    public function incrBy(string $key, int $value): int;

    /**
     * 将 key 所储存的值加上给定的浮点增量值
     * @param string $key
     * @param float $value
     * @return float
     */
    public function incrByFloat(string $key, float $value): float;

    /**
     * 将 key 中储存的数字值减一
     * @param string $key
     * @return int
     */
    public function decr(string $key): int;

    /**
     * key 所储存的值减去给定的减量值
     * @param string $key
     * @param int $value
     * @return int
     */
    public function decrBy(string $key, int $value): int;

    /**
     * 如果 key 已经存在并且是一个字符串， APPEND 命令将指定的 value 追加到该 key 原来值（value）的末尾，返回字符长度
     * @param string $key
     * @param $value
     * @return int
     */
    public function append(string $key, $value): int;

    /**
     * 将当前数据库的 key 移动到给定的数据库 db 当中
     * @param string $key
     * @param int $dbIndex
     * @return bool
     */
    public function move(string $key, int $dbIndex): bool;

    /**
     * 移除给定 key 的过期时间，使得 key 永不过期
     * @param string $key
     * @return bool
     */
    public function persist(string $key): bool;

    /**
     * 删除已存在的键
     * @param string $key
     * @return int
     */
    public function del(string $key): int;

    /**
     * 清空当前数据库中的所有 key
     * @return bool
     */
    public function clean(): bool;

    /**
     * 清空所有数据库中的所有 key
     * @return bool
     */
    public function cleanAll(): bool;

    /**
     * 数据备份
     * @return bool
     */
    public function save(): bool;
}