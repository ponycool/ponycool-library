<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/7/9
 * Time: 3:57 下午
 */
declare(strict_types=1);

namespace PonyCool\Redis;

use Redis;
use Exception;
use RedisException;


class Builder implements OperationsInterface
{
    private int $db;
    private Redis $connect;
    private Config $config;

    /**
     * @throws Exception
     */
    public function __construct(Config $config, int $db)
    {
        $this->config = $config;
        // 初始化客户端
        $conn = new Connection($config);
        $this->connect = $conn->connect();
        // 初始化数据库
        $this->db = $db;
    }

    /**
     * @return Redis
     * @throws Exception
     */
    public function getConnect(): Redis
    {
        try {
            if (true !== $this->connect->ping()) {
                $conn = new Connection($this->config);
                $this->setConnect($conn->connect());
            }
            $this->connect->select($this->db);
            return $this->connect;
        } catch (Exception $e) {
            throw new Exception(sprintf('Redis连接异常，error：%s', $e->getMessage()));
        }

    }

    /**
     * @param Redis $connect
     * @return Builder
     */
    public function setConnect(Redis $connect): Builder
    {
        $this->connect = $connect;
        return $this;
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
     * @return Builder
     */
    public function setDb(int $db): Builder
    {
        $this->db = $db;
        return $this;
    }

    /**
     * 选择数据库
     * @param int $db
     * @return $this
     * @throws RedisException
     */
    public function selectDb(int $db): Builder
    {
        $this->connect->select($db);
        return $this;
    }

    public function ping(): bool
    {
        try {
            return $this->connect->ping();
        } catch (RedisException) {
            return false;
        }
    }

    /**
     * 保存键值
     * @param string $key
     * @param $data
     * @param int|null $ttl
     * @return bool
     * @throws RedisException
     */
    public function set(string $key, $data, ?int $ttl = null): bool
    {
        $db = $this->connect;
        return $db->set($key, $data, $ttl);
    }

    /**
     * 为哈希表中的字段赋值
     * @param string $key
     * @param string $hashKey
     * @param string $value
     * @return bool|int
     * @throws RedisException
     */
    public function hSet(string $key, string $hashKey, string $value): bool|int
    {
        $db = $this->connect;
        return $db->hSet($key, $hashKey, $value);
    }

    /**
     * 同时将多个 field-value (字段-值)对设置到哈希表中
     * @param string $key
     * @param array $hashKeys
     * @return bool
     * @throws RedisException
     */
    public function hMSet(string $key, array $hashKeys): bool
    {
        $db = $this->connect;
        return $db->hMSet($key, $hashKeys);
    }

    /**
     * 设置 key 的过期时间
     * @param string $key
     * @param int $ttl
     * @return bool
     * @throws RedisException
     */
    public function expire(string $key, int $ttl): bool
    {
        $db = $this->connect;
        return $db->expire($key, $ttl);
    }

    /**
     * 设置 key 的过期时间，以毫秒为单位设置 key 的生存时间
     * @param string $key
     * @param int $ttl
     * @return bool
     * @throws RedisException
     */
    public function pExpire(string $key, int $ttl): bool
    {
        $db = $this->connect;
        return $db->pExpire($key, $ttl);
    }

    /**
     * 以 UNIX 时间戳(unix timestamp)格式设置 key 的过期时间
     * @param string $key
     * @param int $timestamp
     * @return bool
     * @throws RedisException
     */
    public function expireAt(string $key, int $timestamp): bool
    {
        $db = $this->connect;
        return $db->expireAt($key, $timestamp);
    }

    /**
     * 以 UNIX 时间戳(unix timestamp)格式设置 key 的过期时间，以毫秒计
     * @param string $key
     * @param int $timestamp
     * @return bool
     * @throws RedisException
     */
    public function pExpireAt(string $key, int $timestamp): bool
    {
        $db = $this->connect;
        return $db->pExpireAt($key, $timestamp);
    }

    /**
     * 获取指定 key 的值
     * @param string $key
     * @return mixed
     * @throws RedisException
     */
    public function get(string $key): mixed
    {
        $db = $this->connect;
        return $db->get($key);
    }

    /**
     * 获取存储在指定 key 中字符串的子字符串
     * @param string $key
     * @param int $start
     * @param int $end
     * @return string
     * @throws RedisException
     */
    public function getRange(string $key, int $start, int $end): string
    {
        $db = $this->connect;
        return $db->getRange($key, $start, $end);
    }

    /**
     * 设置指定 key 的值，并返回 key 的旧值
     * @param string $key
     * @param string $value
     * @return mixed
     * @throws RedisException
     */
    public function getSet(string $key, string $value): mixed
    {
        $db = $this->connect;
        return $db->getSet($key, $value);
    }

    /**
     * 以秒为单位返回 key 的剩余过期时间
     * @param string $key
     * @return bool|int
     * @throws RedisException
     */
    public function getTtl(string $key): bool|int
    {
        $db = $this->connect;
        return $db->ttl($key);
    }

    /**
     * 以毫秒为单位返回 key 的剩余的过期时间
     * @param string $key
     * @return bool|int
     * @throws RedisException
     */
    public function getPTtl(string $key): bool|int
    {
        $db = $this->connect;
        return $db->pttl($key);
    }

    /**
     * 从当前数据库中随机返回一个 key
     * @return string
     * @throws RedisException
     */
    public function getRandomKey(): string
    {
        $db = $this->connect;
        return $db->randomKey();
    }

    /**
     * 返回 key 所储存的值的类型
     * @param string $key
     * @return int
     * @throws RedisException
     */
    public function getType(string $key): int
    {
        $db = $this->connect;
        return $db->type($key);
    }

    /**
     * 查找所有符合给定模式 pattern 的 key
     * @param string $pattern
     * @return array
     * @throws RedisException
     */
    public function keys(string $pattern): array
    {
        $db = $this->connect;
        return $db->keys($pattern);
    }

    /**
     * 返回哈希表所有的值
     * @param string $key
     * @return array
     * @throws RedisException
     */
    public function hVals(string $key): array
    {
        $db = $this->connect;
        return $db->hVals($key);
    }

    /**
     * 检查给定 key 是否存在
     * @param string $key
     * @return bool
     * @throws RedisException
     */
    public function exists(string $key): bool
    {
        $db = $this->connect;
        return (bool)$db->exists($key);
    }

    /**
     * 查看哈希表 key 中，指定的字段是否存在
     * @param string $key
     * @param string $hashKey
     * @return bool
     * @throws RedisException
     */
    public function hExists(string $key, string $hashKey): bool
    {
        $db = $this->connect;
        return $db->hExists($key, $hashKey);
    }

    /**
     * 修改 key 的名称
     * @param string $srcKey
     * @param string $dstKey
     * @return bool
     * @throws RedisException
     */
    public function rename(string $srcKey, string $dstKey): bool
    {
        $db = $this->connect;
        return $db->rename($srcKey, $dstKey);
    }

    /**
     * 新的 key 不存在时修改 key 的名称
     * @param string $srcKey
     * @param string $dstKey
     * @return bool
     * @throws RedisException
     */
    public function renameNx(string $srcKey, string $dstKey): bool
    {
        $db = $this->connect;
        return $db->renameNx($srcKey, $dstKey);
    }

    /**
     * 将 key 中储存的数字值增一
     * @param string $key
     * @return int
     * @throws RedisException
     */
    public function incr(string $key): int
    {
        $db = $this->connect;
        return $db->incr($key);
    }

    /**
     * 将 key 所储存的值加上给定的增量值
     * @param string $key
     * @param int $value
     * @return int
     * @throws RedisException
     */
    public function incrBy(string $key, int $value): int
    {
        $db = $this->connect;
        return $db->incrBy($key, $value);
    }

    /**
     * 将 key 所储存的值加上给定的浮点增量值
     * @param string $key
     * @param float $value
     * @return float
     * @throws RedisException
     */
    public function incrByFloat(string $key, float $value): float
    {
        $db = $this->connect;
        return $db->incrByFloat($key, $value);
    }

    /**
     * 将 key 中储存的数字值减一
     * @param string $key
     * @return int
     * @throws RedisException
     */
    public function decr(string $key): int
    {
        $db = $this->connect;
        return $db->decr($key);
    }

    /**
     * key 所储存的值减去给定的减量值
     * @param string $key
     * @param int $value
     * @return int
     * @throws RedisException
     */
    public function decrBy(string $key, int $value): int
    {
        $db = $this->connect;
        return $db->decrBy($key, $value);
    }

    /**
     * 如果 key 已经存在并且是一个字符串， APPEND 命令将指定的 value 追加到该 key 原来值（value）的末尾，返回字符长度
     * @param string $key
     * @param $value
     * @return int
     * @throws RedisException
     */
    public function append(string $key, $value): int
    {
        $db = $this->connect;
        return $db->append($key, $value);
    }

    /**
     * 将当前数据库的 key 移动到给定的数据库 db 当中
     * @param string $key
     * @param int $dbIndex
     * @return bool
     * @throws RedisException
     */
    public function move(string $key, int $dbIndex): bool
    {
        $db = $this->connect;
        return $db->move($key, $dbIndex);
    }

    /**
     * 移除给定 key 的过期时间，使得 key 永不过期
     * @param string $key
     * @return bool
     * @throws RedisException
     */
    public function persist(string $key): bool
    {
        $db = $this->connect;
        return $db->persist($key);
    }

    /**
     * 删除已存在的键
     * @param string $key
     * @return int
     * @throws RedisException
     */
    public function del(string $key): int
    {
        $db = $this->connect;
        return $db->del($key);
    }

    /**
     * 清空当前数据库中的所有 key
     * @return bool
     * @throws RedisException
     */
    public function clean(): bool
    {
        $db = $this->connect;
        return $db->flushDB();
    }

    /**
     * 清空所有数据库中的所有 key
     * @return bool
     * @throws RedisException
     */
    public function cleanAll(): bool
    {
        $db = $this->connect;
        return $db->flushAll();
    }

    /**
     * 数据备份
     * @return bool
     * @throws RedisException
     */
    public function save(): bool
    {
        $db = $this->connect;
        return $db->save();
    }
}