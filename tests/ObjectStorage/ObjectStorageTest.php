<?php
/**
 * Created By PhpStorm
 * User: Pony
 * Data: 2023/3/14
 * Time: 10:28
 */

namespace ObjectStorage;

use Exception;
use PHPUnit\Framework\TestCase;
use PonyCool\ObjectStorage\ObjectStorage;
use PonyCool\ObjectStorage\ObjectStorageFactory;

class ObjectStorageTest extends TestCase
{
    /**
     * 测试初始化对象存储工厂
     * @test
     * @return object
     * @throws Exception
     */
    public function testObjectStorageFactory(): object
    {
        $source = 'oss';
        $objectStorage = new ObjectStorage();
        $objectStorage->setAccessKey('testKey')
            ->setSecret('testSecret')
            ->setRegion('testRegion')
            ->setBucket('testBucket')
            ->setCallbackUrl('https://test.com/callback');
        $ObjectStorageFactory = new ObjectStorageFactory();
        $factory = $ObjectStorageFactory::factory($source, $objectStorage);
        self::assertIsObject($factory, '对象存储工厂方法初始化失败');
        return $factory;
    }

    /**
     * 测试获取直传签名
     * @test
     * @depends testObjectStorageFactory
     * @param object $ob
     * @return void
     */
    public function testGetSignature(object $ob): void
    {
        $signature = $ob->getSignature();
        self::assertIsString($signature, '获取直传签名未达到预期');
    }
}