<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/7/9
 * Time: 3:56 下午
 */
declare(strict_types=1);

namespace PonyCool\Redis;

use Exception;
use RedisException;


class Client implements OperationsInterface
{
    protected int $db;
    protected Builder $builder;
    private Config $conf;

    /**
     * 初始化客户端
     * @throws Exception
     */
    public function __construct(Config $config)
    {
        if (!extension_loaded('redis')) {
            throw new Exception('Redis扩展未安装或未加载');
        }
        $this->conf = $config;

        // 初始化数据库
        $this->setDb($config->getDb());

        $builder = new Builder($this->conf, $this->db);
        $this->setBuilder($builder);
    }

    /**
     * @return int
     */
    public function getDb(): int
    {
        return $this->db;
    }

    /**
     * @param int $db
     * @return Client
     */
    public function setDb(int $db): Client
    {
        $this->db = $db;
        return $this;
    }

    /**
     * @return Builder
     * @throws Exception
     */
    public function getBuilder(): Builder
    {
        return $this->builder;
    }

    /**
     * @param Builder $builder
     * @return Client
     */
    public function setBuilder(Builder $builder): Client
    {
        $this->builder = $builder;
        return $this;
    }

    /**
     * 选择数据库
     * @throws Exception
     */
    public function selectDb(int $db): Client
    {
        $this->builder->selectDb($db);
        return $this;
    }

    /**
     * @return bool
     */
    public function ping(): bool
    {
        try {
            $builder = $this->getBuilder();
            return $builder->ping();
        } catch (Exception) {
            return false;
        }
    }

    /**
     * 保存键值
     * @throws Exception
     */
    public function set(string $key, $data, ?int $ttl = null): bool
    {
        $builder = $this->getBuilder();
        return $builder->set($key, $data, $ttl);
    }

    /**
     * 为哈希表中的字段赋值
     * @param string $key
     * @param string $hashKey
     * @param string $value
     * @return bool|int
     * @throws Exception
     */
    public function hSet(string $key, string $hashKey, string $value): bool|int
    {
        $builder = $this->getBuilder();
        return $builder->hSet($key, $hashKey, $value);
    }

    /**
     * 同时将多个 field-value (字段-值)对设置到哈希表中
     * @param string $key
     * @param array $hashKeys
     * @return bool
     * @throws Exception
     */
    public function hMSet(string $key, array $hashKeys): bool
    {
        $builder = $this->getBuilder();
        return $builder->hMSet($key, $hashKeys);
    }

    /**
     * 设置 key 的过期时间
     * @param string $key
     * @param int $ttl
     * @return bool
     * @throws Exception
     */
    public function expire(string $key, int $ttl): bool
    {
        $builder = $this->getBuilder();
        return $builder->expire($key, $ttl);
    }

    /**
     * 设置 key 的过期时间，以毫秒为单位设置 key 的生存时间
     * @param string $key
     * @param int $ttl
     * @return bool
     * @throws Exception
     */
    public function pExpire(string $key, int $ttl): bool
    {
        $builder = $this->getBuilder();
        return $builder->pExpire($key, $ttl);
    }

    /**
     * 以 UNIX 时间戳(unix timestamp)格式设置 key 的过期时间
     * @param string $key
     * @param int $timestamp
     * @return bool
     * @throws Exception
     */
    public function expireAt(string $key, int $timestamp): bool
    {
        $builder = $this->getBuilder();
        return $builder->expireAt($key, $timestamp);
    }

    /**
     * 以 UNIX 时间戳(unix timestamp)格式设置 key 的过期时间，以毫秒计
     * @param string $key
     * @param int $timestamp
     * @return bool
     * @throws Exception
     */
    public function pExpireAt(string $key, int $timestamp): bool
    {
        $builder = $this->getBuilder();
        return $builder->pExpireAt($key, $timestamp);
    }

    /**
     * 获取指定 key 的值
     * @param string $key
     * @return mixed
     */
    public function get(string $key): mixed
    {
        try {
            $builder = $this->getBuilder();
            return $builder->get($key);
        } catch (Exception) {
            return false;
        }
    }

    /**
     * 获取存储在指定 key 中字符串的子字符串
     * @param string $key
     * @param int $start
     * @param int $end
     * @return string
     * @throws Exception
     */
    public function getRange(string $key, int $start, int $end): string
    {
        $builder = $this->getBuilder();
        return $builder->getRange($key, $start, $end);
    }

    /**
     * 设置指定 key 的值，并返回 key 的旧值
     * @param string $key
     * @param string $value
     * @return mixed|string
     * @throws Exception
     */
    public function getSet(string $key, string $value): mixed
    {
        $builder = $this->getBuilder();
        return $builder->getSet($key, $value);
    }

    /**
     * 以秒为单位返回 key 的剩余过期时间
     * @param string $key
     * @return bool|int
     * @throws Exception
     */
    public function getTtl(string $key): bool|int
    {
        $builder = $this->getBuilder();
        return $builder->getTtl($key);
    }

    /**
     * 以毫秒为单位返回 key 的剩余的过期时间
     * @param string $key
     * @return bool|int
     * @throws Exception
     */
    public function getPTtl(string $key): bool|int
    {
        $builder = $this->getBuilder();
        return $builder->getPTtl($key);
    }

    /**
     * 从当前数据库中随机返回一个 key
     * @return string
     * @throws Exception
     */
    public function getRandomKey(): string
    {
        $builder = $this->getBuilder();
        return $builder->getRandomKey();
    }

    /**
     * 返回 key 所储存的值的类型
     * @param string $key
     * @return int
     * @throws Exception
     */
    public function getType(string $key): int
    {
        $builder = $this->getBuilder();
        return $builder->getType($key);
    }

    /**
     * 查找所有符合给定模式 pattern 的 key
     * @param string $pattern
     * @return array
     * @throws Exception
     */
    public function keys(string $pattern): array
    {
        $builder = $this->getBuilder();
        return $builder->keys($pattern);
    }

    /**
     * 返回哈希表所有的值
     * @param string $key
     * @return array
     * @throws Exception
     */
    public function hVals(string $key): array
    {
        $builder = $this->getBuilder();
        return $builder->hVals($key);
    }

    /**
     * 检查给定 key 是否存在
     * @param string $key
     * @return bool
     * @throws Exception
     */
    public function exists(string $key): bool
    {
        $builder = $this->getBuilder();
        return $builder->exists($key);
    }

    /**
     * 查看哈希表 key 中，指定的字段是否存在
     * @param string $key
     * @param string $hashKey
     * @return bool
     * @throws Exception
     */
    public function hExists(string $key, string $hashKey): bool
    {
        $builder = $this->getBuilder();
        return $builder->hExists($key, $hashKey);
    }

    /**
     * 修改 key 的名称
     * @param string $srcKey
     * @param string $dstKey
     * @return bool
     * @throws Exception
     */
    public function rename(string $srcKey, string $dstKey): bool
    {
        $builder = $this->getBuilder();
        return $builder->rename($srcKey, $dstKey);
    }

    /**
     * 新的 key 不存在时修改 key 的名称
     * @param string $srcKey
     * @param string $dstKey
     * @return bool
     * @throws Exception
     */
    public function renameNx(string $srcKey, string $dstKey): bool
    {
        $builder = $this->getBuilder();
        return $builder->renameNx($srcKey, $dstKey);
    }

    /**
     * 将 key 中储存的数字值增一
     * @param string $key
     * @return int
     * @throws Exception
     */
    public function incr(string $key): int
    {
        $builder = $this->getBuilder();
        return $builder->incr($key);
    }

    /**
     * 将 key 所储存的值加上给定的增量值
     * @param string $key
     * @param int $value
     * @return int
     * @throws Exception
     */
    public function incrBy(string $key, int $value): int
    {
        $builder = $this->getBuilder();
        return $builder->incrBy($key, $value);
    }

    /**
     * 将 key 所储存的值加上给定的浮点增量值
     * @param string $key
     * @param float $value
     * @return float
     * @throws Exception
     */
    public function incrByFloat(string $key, float $value): float
    {
        $builder = $this->getBuilder();
        return $builder->incrByFloat($key, $value);
    }

    /**
     * 将 key 中储存的数字值减一
     * @param string $key
     * @return int
     * @throws Exception
     */
    public function decr(string $key): int
    {
        $builder = $this->getBuilder();
        return $builder->decr($key);
    }

    /**
     * key 所储存的值减去给定的减量值
     * @param string $key
     * @param int $value
     * @return int
     * @throws Exception
     */
    public function decrBy(string $key, int $value): int
    {
        $builder = $this->getBuilder();
        return $builder->decrBy($key, $value);
    }

    /**
     * 如果 key 已经存在并且是一个字符串， APPEND 命令将指定的 value 追加到该 key 原来值（value）的末尾，返回字符长度
     * @param string $key
     * @param $value
     * @return int
     * @throws Exception
     */
    public function append(string $key, $value): int
    {
        $builder = $this->getBuilder();
        return $builder->append($key, $value);
    }

    /**
     * 将当前数据库的 key 移动到给定的数据库 db 当中
     * @param string $key
     * @param int $dbIndex
     * @return bool
     * @throws Exception
     */
    public function move(string $key, int $dbIndex): bool
    {
        $builder = $this->getBuilder();
        return $builder->move($key, $dbIndex);
    }

    /**
     * 移除给定 key 的过期时间，使得 key 永不过期
     * @param string $key
     * @return bool
     * @throws Exception
     */
    public function persist(string $key): bool
    {
        $builder = $this->getBuilder();
        return $builder->persist($key);
    }

    /**
     * 删除已存在的键
     * @param string $key
     * @return int
     * @throws Exception
     */
    public function del(string $key): int
    {
        $builder = $this->getBuilder();
        return $builder->del($key);
    }

    /**
     * 清空当前数据库中的所有 key
     * @return bool
     * @throws Exception
     */
    public function clean(): bool
    {
        $builder = $this->getBuilder();
        return $builder->clean();
    }

    /**
     * 清空所有数据库中的所有 key
     * @return bool
     * @throws Exception
     */
    public function cleanAll(): bool
    {
        $builder = $this->getBuilder();
        return $builder->cleanAll();
    }

    /**
     * 数据备份
     * @return bool
     * @throws Exception
     */
    public function save(): bool
    {
        $builder = $this->getBuilder();
        return $builder->save();
    }

    /**
     * 移出并获取列表的第一个元素， 如果列表没有元素会阻塞列表直到等待超时或发现可弹出元素为止
     * @param string $key
     * @param int $timeout
     * @return array
     * @throws Exception|RedisException
     */
    public function blPop(string $key, int $timeout): array
    {
        $builder = $this->getBuilder();
        return $builder->blPop($key, $timeout);
    }

    /**
     * 移出并获取列表的最后一个元素， 如果列表没有元素会阻塞列表直到等待超时或发现可弹出元素为止
     * @param string $key
     * @param int $timeout
     * @return array
     * @throws Exception|RedisException
     */
    public function brPop(string $key, int $timeout): array
    {
        $builder = $this->getBuilder();
        return $builder->brPop($key, $timeout);
    }

    /**
     * 从列表中弹出一个值，将弹出的元素插入到另外一个列表中并返回它； 如果列表没有元素会阻塞列表直到等待超时或发现可弹出元素为止
     * @param string $srcKey
     * @param string $dstKey
     * @param int $timeout
     * @return mixed
     * @throws Exception|RedisException
     */
    public function bRPopLPush(string $srcKey, string $dstKey, int $timeout): mixed
    {
        $builder = $this->getBuilder();
        return $builder->bRPopLPush($srcKey, $dstKey, $timeout);
    }

    /**
     * 通过索引获取列表中的元素
     * @param string $key
     * @param int $index
     * @return mixed
     * @throws Exception|RedisException
     */
    public function lIndex(string $key, int $index): mixed
    {
        $builder = $this->getBuilder();
        return $builder->lIndex($key, $index);
    }

    /**
     * 在列表的元素前或者后插入元素
     * 将值 value 插入到列表 key 当中，位于值 pivot 之前或之后
     * @param string $key
     * @param string $position BEFORE|AFTER
     * @param mixed $pivot
     * @param mixed $value
     * @return false|int
     * @throws Exception|RedisException
     */
    public function lInsert(string $key, string $position, mixed $pivot, mixed $value): false|int
    {
        $builder = $this->getBuilder();
        return $builder->lInsert($key, $position, $pivot, $value);
    }

    /**
     * 获取列表长度
     * @param string $key
     * @return bool|int
     * @throws Exception|RedisException
     */
    public function lLen(string $key): bool|int
    {
        $builder = $this->getBuilder();
        return $builder->lLen($key);
    }

    /**
     * 移出并获取列表的第一个元素
     * @param string $key
     * @return mixed
     * @throws Exception|RedisException
     */
    public function lPop(string $key): mixed
    {
        $builder = $this->getBuilder();
        return $builder->lPop($key);
    }

    /**
     * 将一个或多个值插入到列表头部
     * @param string $key
     * @param mixed ...$value
     * @return false|int
     * @throws Exception|RedisException
     */
    public function lPush(string $key, mixed ...$value): false|int
    {
        $builder = $this->getBuilder();
        return $builder->lPush($key, ...$value);
    }

    /**
     * 将一个值插入到已存在的列表头部
     * @param string $key
     * @param mixed $value
     * @return false|int
     * @throws Exception|RedisException
     */
    public function lPushX(string $key, mixed $value): false|int
    {
        $builder = $this->getBuilder();
        return $builder->lPushx($key, $value);
    }

    /**
     * 获取列表指定范围内的元素
     * @param string $key
     * @param int $start
     * @param int $end
     * @return array
     * @throws Exception|RedisException
     */
    public function lRange(string $key, int $start, int $end): array
    {
        $builder = $this->getBuilder();
        return $builder->lRange($key, $start, $end);
    }

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
     * @throws Exception|RedisException
     */
    public function lRem(string $key, string $value, int $count): bool|int
    {
        $builder = $this->getBuilder();
        return $builder->lRem($key, $value, $count);
    }

    /**
     * 通过索引设置列表元素的值
     * @param string $key
     * @param int $index
     * @param string $value
     * @return bool
     * @throws Exception|RedisException
     */
    public function lSet(string $key, int $index, string $value): bool
    {
        $builder = $this->getBuilder();
        return $builder->lSet($key, $index, $value);
    }

    /**
     * 对一个列表进行修剪(trim)，就是说，让列表只保留指定区间内的元素，不在指定区间之内的元素都将被删除。
     * @param string $key
     * @param int $start
     * @param int $stop
     * @return array|false
     * @throws Exception|RedisException
     */
    public function lTrim(string $key, int $start, int $stop): array|false
    {
        $builder = $this->getBuilder();
        return $builder->lTrim($key, $start, $stop);
    }

    /**
     * 移除列表的最后一个元素，返回值为移除的元素
     * @param string $key
     * @return mixed
     * @throws Exception|RedisException
     */
    public function rPop(string $key): mixed
    {
        $builder = $this->getBuilder();
        return $builder->rPop($key);
    }

    /**
     * 移除列表的最后一个元素，并将该元素添加到另一个列表并返回
     * @param string $srcKey
     * @param string $dstKey
     * @return mixed
     * @throws Exception|RedisException
     */
    public function rPopLPush(string $srcKey, string $dstKey): mixed
    {
        $builder = $this->getBuilder();
        return $builder->rPopLPush($srcKey, $dstKey);
    }

    /**
     * 在列表中添加一个或多个值到列表尾部
     * @param string $key
     * @param mixed ...$value
     * @return false|int
     * @throws Exception|RedisException
     */
    public function rPush(string $key, mixed ...$value): false|int
    {
        $builder = $this->getBuilder();
        return $builder->rPush($key, ...$value);
    }

    /**
     * 为已存在的列表添加值
     * @param string $key
     * @param mixed $value
     * @return false|int
     * @throws Exception|RedisException
     */
    public function rPushX(string $key, mixed $value): false|int
    {
        $builder = $this->getBuilder();
        return $builder->rPushx($key, $value);
    }
}