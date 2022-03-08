<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/7/8
 * Time: 9:51 上午
 */
declare(strict_types=1);

namespace PonyCool\Mongo;


interface OperationsInterface
{

    /**
     * 查询一个文档
     * @param array $filter
     * @param array $options
     * @return object|null
     */
    public function findOne(array $filter = [], array $options = []): ?object;

    /**
     * 查询文档
     * @param array $filter
     * @param array $options
     * @return object
     */
    public function find(array $filter = [], array $options = []): object;

    /**
     * 查询并更新一个文档
     * @param array $filter 过滤条件
     * @param array $data 更新数据
     * @param array $options
     * @return object|null
     */
    public function findOneAndUpdate(array $filter, array $data, array $options = []): ?object;

    /**
     * 查询一个文档并替换，返回原始文档
     * @param array $filter
     * @param array $data
     * @param array $options
     * @return object|null
     */
    public function findOneAndReplace(array $filter, array $data, array $options = []): ?object;

    /**
     * 查询一个文档并删除，返回原始文档
     * @param array $filter
     * @param array $options
     * @return object|null
     */
    public function findOneAndDelete(array $filter, array $options = []): ?object;

    /**
     * 查询文档数量
     * @param array $filter
     * @param array $options
     * @return int
     */
    public function getCount(array $filter = [], array $options = []): int;

    /**
     * 获取集合中指定字段的不重复值，并以数组的形式返回
     * @param string $fieldName
     * @param array $filter
     * @param array $options
     * @return array
     */
    public function getDistinctValues(string $fieldName, array $filter = [], array $options = []): array;

    /**
     * 列出索引
     * @param array $options
     * @return object
     */
    public function listIndexes(array $options = []): object;

    /**
     * 数据聚合
     * @param array $pipeline
     * @param array $options
     * @return object
     */
    public function aggregate(array $pipeline, array $options = []): object;

    /**
     * 执行命令
     * @param array $command
     * @param array $options
     * @return array
     */
    public function command(array $command, array $options = []): array;

    /**
     * 插入一个文档
     * @param array $document 文档
     * @param array $options 命令参数
     * @return object
     */
    public function insert(array $document, array $options = []): object;

    /**
     * 插入多个文档
     * @param array $document
     * @param array $options
     * @return array
     */
    public function batchInsert(array $document, array $options = []): array;

    /**
     * 创建索引
     * @param array $key
     * @param array $options
     * @return string 返回索引名
     */
    public function createIndex(array $key, array $options = []): string;

    /**
     * 创建一个或多个索引
     * @param array $indexes
     * @param array $options
     * @return array
     */
    public function createIndexes(array $indexes, array $options = []): array;

    /**
     * 更新一个文档
     * @param array $filter
     * @param array $data
     * @param array $options
     * @return bool
     */
    public function update(array $filter, array $data, array $options = []): bool;

    /**
     * 批量更新文档
     * @param array $filter
     * @param array $data
     * @param array $options
     * @return bool
     */
    public function batchUpdate(array $filter, array $data, array $options = []): bool;

    /**
     * 替换一个文档
     * @param array $filter
     * @param array $data
     * @param array $options
     * @return bool
     */
    public function replaceOne(array $filter, array $data, array $options = []): bool;

    /**
     * 删除一个文档
     * @param array $filter
     * @param array $options
     * @return bool
     */
    public function delete(array $filter, array $options = []): bool;

    /**
     * 批量删除文档
     * @param array $filter
     * @param array $options
     * @return bool
     */
    public function batchDelete(array $filter, array $options = []): bool;

    /**
     * 删除一个索引
     * @param string $indexName
     * @param array $options
     * @return object
     */
    public function dropIndex(string $indexName, array $options = []): object;

    /**
     * 删除全部索引
     * @param array $options
     * @return object
     */
    public function dropIndexes(array $options = []): object;

    /**
     * 删除集合
     * @param array $options
     * @return bool
     */
    public function drop(array $options = []): bool;

}