<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/7/8
 * Time: 9:51 上午
 */
declare(strict_types=1);

namespace PonyCool\Mongo;

use MongoDB\{Client, Database, Collection};

class Builder implements OperationsInterface
{

    private Database $db;
    private Collection $coll;
    private Client $connect;

    public function __construct(Config $config, string $db, string $coll)
    {
        // 初始化客户端
        $conn = new Connection($config);
        $this->connect = $conn->connect();
        // 初始化数据库
        $this->db = $this->connect->selectDatabase($db);
        // 初始化集合
        $this->coll = $this->db->selectCollection($coll);

    }

    /**
     * @return Database
     */
    public function getDb(): Database
    {
        return $this->db;
    }

    /**
     * @param Database $db
     * @return Builder
     */
    public function setDb(Database $db): Builder
    {
        $this->db = $db;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getColl(): Collection
    {
        return $this->coll;
    }

    /**
     * @param Collection $coll
     * @return Builder
     */
    public function setColl(Collection $coll): Builder
    {
        $this->coll = $coll;
        return $this;
    }

    /**
     * 切换集合
     * @param string $coll
     * @return $this
     */
    public function selectColl(string $coll): Builder
    {
        $collection = $this->getDb()
            ->selectCollection($coll);
        $this->setColl($collection);
        return $this;
    }

    /**
     * 查询一个文档
     * @param array $filter
     * @param array $options
     * @return object|null
     */
    public function findOne(array $filter = [], array $options = []): ?object
    {
        $coll = $this->getColl();
        return $coll->findOne($filter, $options);
    }

    /**
     * 查询文档
     * @param array $filter
     * @param array $options
     * @return object
     */
    public function find(array $filter = [], array $options = []): object
    {
        $coll = $this->getColl();
        return $coll->find($filter, $options);
    }

    /**
     * 查询并更新一个文档
     * @param array $filter
     * @param array $data
     * @param array $options
     * @return object|null
     */
    public function findOneAndUpdate(array $filter, array $data, array $options = []): ?object
    {
        $coll = $this->getColl();
        return $coll->findOneAndUpdate($filter, $data, $options);
    }

    /**
     * 查询一个文档并替换，返回原始文档
     * @param array $filter
     * @param array $data
     * @param array $options
     * @return object|null
     */
    public function findOneAndReplace(array $filter, array $data, array $options = []): ?object
    {
        $coll = $this->getColl();
        return $coll->findOneAndReplace($filter, $data, $options);
    }

    /**
     * 查询一个文档并删除，返回原始文档
     * @param array $filter
     * @param array $options
     * @return object|null
     */
    public function findOneAndDelete(array $filter, array $options = []): ?object
    {
        $coll = $this->getColl();
        return $coll->findOneAndDelete($filter, $options);
    }

    /**
     * 查询文档数量
     * @param array $filter
     * @param array $options
     * @return int
     */
    public function getCount(array $filter = [], array $options = []): int
    {
        $coll = $this->getColl();
        return $coll->countDocuments($filter, $options);
    }

    /**
     * 获取集合中指定字段的不重复值，并以数组的形式返回
     * @param string $fieldName
     * @param array $filter
     * @param array $options
     * @return array
     */
    public function getDistinctValues(string $fieldName, array $filter = [], array $options = []): array
    {
        $coll = $this->getColl();
        return $coll->distinct($fieldName, $filter, $options);
    }

    /**
     * 列出索引
     * @param array $options
     * @return object
     */
    public function listIndexes(array $options = []): object
    {
        $coll = $this->getColl();
        return $coll->listIndexes($options);
    }

    /**
     * 数据聚合
     * @param array $pipeline
     * @param array $options
     * @return object
     */
    public function aggregate(array $pipeline, array $options = []): object
    {
        $coll = $this->getColl();
        return $coll->aggregate($pipeline, $options);
    }

    /**
     * 执行命令
     * @param array $command
     * @param array $options
     * @return array
     */
    public function command(array $command, array $options = []): array
    {
        $db = $this->getDb();
        $cursor = $db->command($command, $options);
        return $cursor->toArray()[0];
    }

    /**
     * 插入一个文档
     * @param array $document
     * @param array $options
     * @return mixed|string
     */
    public function insert(array $document, array $options = []): object
    {
        $coll = $this->getColl();
        $result = $coll->insertOne($document, $options);
        return $result->getInsertedId();
    }

    /**
     * 插入多个文档
     * @param array $document
     * @param array $options
     * @return array
     */
    public function batchInsert(array $document, array $options = []): array
    {
        $coll = $this->getColl();
        $result = $coll->insertMany($document, $options);
        return $result->getInsertedIds();
    }

    /**
     * 创建索引
     * @param array $key
     * @param array $options
     * @return string 返回索引名
     */
    public function createIndex(array $key, array $options = []): string
    {
        $coll = $this->getColl();
        return $coll->createIndex($key, $options);
    }

    /**
     * 创建一个或多个索引
     * @param array $indexes
     * @param array $options
     * @return array
     */
    public function createIndexes(array $indexes, array $options = []): array
    {
        $coll = $this->getColl();
        return $coll->createIndexes($indexes, $options);
    }

    /**
     * 更新一个文档
     * @param array $filter
     * @param array $data
     * @param array $options
     * @return bool
     */
    public function update(array $filter, array $data, array $options = []): bool
    {
        $coll = $this->getColl();
        $result = $coll->updateOne($filter, $data, $options);
        return $result->isAcknowledged();
    }

    /**
     * 批量更新文档
     * @param array $filter
     * @param array $data
     * @param array $options
     * @return bool
     */
    public function batchUpdate(array $filter, array $data, array $options = []): bool
    {
        $coll = $this->getColl();
        $result = $coll->updateMany($filter, $data, $options);
        return $result->isAcknowledged();
    }

    /**
     * 替换一个文档
     * @param array $filter
     * @param array $data
     * @param array $options
     * @return bool
     */
    public function replaceOne(array $filter, array $data, array $options = []): bool
    {
        $coll = $this->getColl();
        $result = $coll->replaceOne($filter, $data, $options);
        return $result->isAcknowledged();
    }

    /**
     * 删除一个文档
     * @param array $filter
     * @param array $options
     * @return bool
     */
    public function delete(array $filter, array $options = []): bool
    {
        $coll = $this->getColl();
        $result = $coll->deleteOne($filter, $options);
        return $result->isAcknowledged();
    }

    /**
     * 批量删除文档
     * @param array $filter
     * @param array $options
     * @return bool
     */
    public function batchDelete(array $filter, array $options = []): bool
    {
        $coll = $this->getColl();
        $result = $coll->deleteMany($filter, $options);
        return $result->isAcknowledged();
    }

    /**
     * 删除一个索引
     * @param string $indexName
     * @param array $options
     * @return object
     */
    public function dropIndex(string $indexName, array $options = []): object
    {
        $coll = $this->getColl();
        return $coll->dropIndex($indexName, $options);
    }

    /**
     * 删除全部索引
     * @param array $options
     * @return object
     */
    public function dropIndexes(array $options = []): object
    {
        $coll = $this->getColl();
        return $coll->dropIndexes($options);
    }

    /**
     * 删除集合
     * @param array $options
     * @return bool
     */
    public function drop(array $options = []): bool
    {
        $coll = $this->getColl();
        $result = $coll->drop($options);
        return (bool)$result->ok;
    }
}