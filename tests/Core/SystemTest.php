<?php
/**
 * Created By PhpStorm
 * User: Pony
 * Data: 2024/3/16
 * Time: 11:10
 */
declare(strict_types=1);

namespace Core;

use Exception;
use PHPUnit\Framework\TestCase;
use PonyCool\Core\SystemUtil;

class SystemTest extends TestCase
{
    /**
     * 测试获取系统信息
     * @test
     * @return void
     */
    public function testGetSystemInfo()
    {
        try {
            $systemUtil = new SystemUtil();
            $res = $systemUtil::systemInfo();
            self::assertIsArray($res);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}