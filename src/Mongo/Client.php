<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/7/7
 * Time: 4:47 下午
 */
declare(strict_types=1);

namespace PonyCool\Mongo;

use Exception;
use MongoDB\Client as MongoClient;

class Client implements OperationsInterface
{
    protected string $db;
    // 集合
    protected string $coll;
    protected Builder $builder;
    private Config $conf;


    /**
     * 初始化客户端
     * @throws Exception
     */
    public function __construct(Config $config)
    {
        if (!extension_loaded('mongodb')) {
            throw new Exception('MongoDB扩展未安装或未加载');
        }
        if (!class_exists(MongoClient::class)) {
            throw new Exception('缺少依赖mongodb/mongodb');
        }
        $this->conf = $config;

        // 初始化数据库
        $this->setDb($config->getDb());
    }

    /**
     * @return string
     */
    public function getDb(): string
    {
        return $this->db;
    }

    /**
     * @param string $db
     * @return Client
     */
    public function setDb(string $db): Client
    {
        $this->db = $db;
        return $this;
    }

    /**
     * @return string
     */
    public function getColl(): string
    {
        return $this->coll;
    }

    /**
     * @param string $coll
     * @return Client
     */
    public function setColl(string $coll): Client
    {
        $this->coll = $coll;
        return $this;
    }

    /**
     * @return Builder
     */
    public function getBuilder(): Builder
    {
        $builder = new Builder($this->conf, $this->db, $this->coll);
        $this->setBuilder($builder);
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
     * 查询一个文档
     * @param array $filter
     * @param array $options
     * @return object|null
     */
    public function findOne(array $filter = [], array $options = []): ?object
    {
        $builder = $this->getBuilder();
        return $builder->findOne($filter, $options);
    }

    /**
     * 查询文档
     * @param array $filter
     * @param array $options
     * @return object
     */
    public function find(array $filter = [], array $options = []): object
    {
        $builder = $this->getBuilder();
        return $builder->find($filter, $options);
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
        $builder = $this->getBuilder();
        return $builder->findOneAndUpdate($filter, $data, $options);
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
        $builder = $this->getBuilder();
        return $builder->findOneAndReplace($filter, $data, $options);
    }

    /**
     * 查询一个文档并删除，返回原始文档
     * @param array $filter
     * @param array $options
     * @return object|null
     */
    public function findOneAndDelete(array $filter, array $options = []): ?object
    {
        $builder = $this->getBuilder();
        return $builder->findOneAndDelete($filter, $options);
    }

    /**
     * 查询文档数量
     * @param array $filter
     * @param array $options
     * @return int
     */
    public function getCount(array $filter = [], array $options = []): int
    {
        $builder = $this->getBuilder();
        return $builder->getCount($filter, $options);
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
        $builder = $this->getBuilder();
        return $builder->getDistinctValues($fieldName, $filter, $options);
    }

    /**
     * 列出索引
     * @param array $options
     * @return object
     */
    public function listIndexes(array $options = []): object
    {
        $builder = $this->getBuilder();
        return $builder->listIndexes($options);
    }

    /**
     * 数据聚合
     * @param array $pipeline
     * @param array $options
     * @return object
     */
    public function aggregate(array $pipeline, array $options = []): object
    {
        $builder = $this->getBuilder();
        return $builder->aggregate($pipeline, $options);
    }

    /**
     * 执行命令
     * @param array $command
     * @param array $options
     * @return array
     */
    public function command(array $command, array $options = []): array
    {
        $builder = $this->getBuilder();
        return $builder->command($command, $options);
    }

    /**
     * 插入一个文档
     * @param array $document
     * @param array $options
     * @return object
     */
    public function insert(array $document, array $options = []): object
    {
        $builder = $this->getBuilder();
        return $builder->insert($document, $options);
    }

    /**
     * 插入多个文档
     * @param array $document
     * @param array $options
     * @return array
     */
    public function batchInsert(array $document, array $options = []): array
    {
        $builder = $this->getBuilder();
        return $builder->batchInsert($document, $options);
    }

    /**
     * 创建索引
     * @param array $key
     * @param array $options
     * @return string 返回索引名
     */
    public function createIndex(array $key, array $options = []): string
    {
        $builder = $this->getBuilder();
        return $builder->createIndex($key, $options);
    }

    /**
     * 创建一个或多个索引
     * @param array $indexes
     * @param array $options
     * @return array
     */
    public function createIndexes(array $indexes, array $options = []): array
    {
        $builder = $this->getBuilder();
        return $builder->createIndexes($indexes, $options);
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
        $builder = $this->getBuilder();
        return $builder->update($filter, $data, $options);
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
        $builder = $this->getBuilder();
        return $builder->batchUpdate($filter, $data, $options);
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
        $builder = $this->getBuilder();
        return $builder->replaceOne($filter, $data, $options);
    }

    /**
     * 删除一个文档
     * @param array $filter
     * @param array $options
     * @return bool
     */
    public function delete(array $filter, array $options = []): bool
    {
        $builder = $this->getBuilder();
        return $builder->delete($filter, $options);
    }

    /**
     * 批量删除文档
     * @param array $filter
     * @param array $options
     * @return bool
     */
    public function batchDelete(array $filter, array $options = []): bool
    {
        $builder = $this->getBuilder();
        return $builder->batchDelete($filter, $options);
    }

    /**
     * 删除一个索引
     * @param string $indexName
     * @param array $options
     * @return object
     */
    public function dropIndex(string $indexName, array $options = []): object
    {
        $builder = $this->getBuilder();
        return $builder->dropIndex($indexName, $options);
    }

    /**
     * 删除全部索引
     * @param array $options
     * @return object
     */
    public function dropIndexes(array $options = []): object
    {
        $builder = $this->getBuilder();
        return $builder->dropIndexes($options);
    }

    /**
     * 删除集合
     * @param array $options
     * @return bool
     */
    public function drop(array $options = []): bool
    {
        $builder = $this->getBuilder();
        return $builder->drop($options);
    }
}