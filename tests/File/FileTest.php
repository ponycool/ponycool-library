<?php
/**
 * Created By PhpStorm
 * User: Pony
 * Data: 2024/3/16
 * Time: 09:03
 */
declare(strict_types=1);

namespace File;

use PHPUnit\Framework\TestCase;
use PonyCool\File\File as FileHelper;

class FileTest extends TestCase
{
    /**
     * 获取文件最后N行数据
     * @test
     * @return void
     */
    public function testGetFileLastLines(): void
    {
        $file = __FILE__;
        $fileHelper = new FileHelper();
        $res = $fileHelper::getFileLastLines($file, 5);
        self::assertIsNotBool($res, '读取文件最后N行数据结果未达到预期');
    }

    /**
     * 分页获取目录下的文件
     * @return void
     */
    public function testPaginateFiles(): void
    {
        $path = __DIR__;
        $fileHelper = new FileHelper();
        $res = $fileHelper::paginateFiles($path);
        self::assertIsArray($res);
    }
}