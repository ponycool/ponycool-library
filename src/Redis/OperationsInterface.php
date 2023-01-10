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

    /**
     * 移出并获取列表的第一个元素， 如果列表没有元素会阻塞列表直到等待超时或发现可弹出元素为止
     * @param string $key
     * @param int $timeout
     * @return array
     */
    public function blPop(string $key, int $timeout): array;

    /**
     * 移出并获取列表的最后一个元素， 如果列表没有元素会阻塞列表直到等待超时或发现可弹出元素为止
     * @param string $key
     * @param int $timeout
     * @return array
     */
    public function brPop(string $key, int $timeout): array;


    /**
     * 从列表中弹出一个值，将弹出的元素插入到另外一个列表中并返回它； 如果列表没有元素会阻塞列表直到等待超时或发现可弹出元素为止
     * @param string $srcKey
     * @param string $dstKey
     * @param int $timeout
     * @return mixed
     */
    public function bRPopLPush(string $srcKey, string $dstKey, int $timeout): mixed;

    /**
     * 通过索引获取列表中的元素
     * @param string $key
     * @param int $index
     * @return mixed
     */
    public function lIndex(string $key, int $index): mixed;

    /**
     * 在列表的元素前或者后插入元素
     * 将值 value 插入到列表 key 当中，位于值 pivot 之前或之后
     * @param string $key
     * @param string $position BEFORE|AFTER
     * @param mixed $pivot
     * @param mixed $value
     * @return false|int
     */
    public function lInsert(string $key, string $position, mixed $pivot, mixed $value): false|int;


    /**
     * 获取列表长度
     * @param string $key
     * @return bool|int
     */
    public function lLen(string $key): bool|int;

    /**
     * 移出并获取列表的第一个元素
     * @param string $key
     * @return mixed
     */
    public function lPop(string $key): mixed;

    /**
     * 将一个或多个值插入到列表头部
     * @param string $key
     * @param mixed ...$value
     * @return false|int
     */
    public function lPush(string $key, mixed ...$value): false|int;

    /**
     * 将一个值插入到已存在的列表头部
     * @param string $key
     * @param mixed $value
     * @return false|int
     */
    public function lPushX(string $key, mixed $value): false|int;

    /**
     * 获取列表指定范围内的元素
     * @param string $key
     * @param int $start
     * @param int $end
     * @return array
     */
    public function lRange(string $key, int $start, int $end): array;

    /**
     * 移除列表元素
     * Redis Lrem 根据参数 COUNT 的值，移除列表中与参数 VALUE 相等的元素。
     * COUNT 的值可以是以下几种：
     * count > 0 : 从表头开始向表尾搜索，移除与 VALUE 相等的元素，数量为 COUNT 。
     * count < 0 : 从表尾开始向表头搜索，移除与 VALUE 相等的元素，数量为 COUNT 的绝对值
     * count = 0 : 移除表中所有与 VALUE 相等的值。
     * @param string $key
     * @param string $value
     * @param int $count
     * @return bool|int
     */
    public function lRem(string $key, string $value, int $count): bool|int;

    /**
     * 通过索引设置列表元素的值
     * @param string $key
     * @param int $index
     * @param string $value
     * @return bool
     */
    public function lSet(string $key, int $index, string $value): bool;

    /**
     * 对一个列表进行修剪(trim)，就是说，让列表只保留指定区间内的元素，不在指定区间之内的元素都将被删除。
     * @param string $key
     * @param int $start
     * @param int $stop
     * @return array|false
     */
    public function lTrim(string $key, int $start, int $stop): array|false;

    /**
     * 移除列表的最后一个元素，返回值为移除的元素
     * @param string $key
     * @return mixed
     */
    public function rPop(string $key): mixed;

    /**
     * 移除列表的最后一个元素，并将该元素添加到另一个列表并返回
     * @param string $srcKey
     * @param string $dstKey
     * @return mixed
     */
    public function rPopLPush(string $srcKey, string $dstKey): mixed;

    /**
     * 在列表中添加一个或多个值到列表尾部
     * @param string $key
     * @param mixed ...$value
     * @return false|int
     */
    public function rPush(string $key, mixed ...$value): false|int;

    /**
     * 为已存在的列表添加值
     * @param string $key
     * @param mixed $value
     * @return false|int
     */
    public function rPushX(string $key, mixed $value): false|int;
}