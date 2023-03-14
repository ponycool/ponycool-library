<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/9/24
 * Time: 11:22 上午
 */
declare(strict_types=1);

namespace PonyCool\ObjectStorage;

use ReflectionClass;
use ReflectionException;
use Exception;

class ObjectStorageFactory
{
    /**
     * ObjectStorage工厂
     * @param string $source
     * @param ObjectStorage $objectStorage
     * @return object
     * @throws Exception
     */
    public static function factory(string $source, ObjectStorage $objectStorage): object
    {
        try {
            if ($objectStorage->check() !== true) {
                throw new Exception('Object Storage 配置无效');
            }
            $os = new ReflectionClass(__NAMESPACE__ . '\\' . ucfirst($source));
            if (!$os->isSubclassOf(__NAMESPACE__ . '\\ObjectStorageInterface')) {
                throw new ReflectionException($source . "未实现ObjectStorage接口类");
            }
            return $os->newInstance($objectStorage);
        } catch (ReflectionException|Exception $e) {
            $message = sprintf('%s加载失败，error：%s', $source, $e->getMessage());
            throw new Exception($message);
        }
    }
}
