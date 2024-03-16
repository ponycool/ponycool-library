<?php
/**
 * Created By PhpStorm
 * User: Pony
 * Data: 2024/3/16
 * Time: 10:08
 */
declare(strict_types=1);

namespace Core;

use PHPUnit\Framework\TestCase;
use PonyCool\Core\ArrayUtil;

class ArrayTest extends TestCase
{
    /**
     * 获取字符串的所有排列组合数组
     * @test
     * @return void
     */
    public function testGetCombination()
    {
        $str = 'ABC';
        $res = ArrayUtil::getCombination($str);
        self::assertIsArray($res);
    }
}