<?php
declare(strict_types=1);

namespace Jwt;

use Exception;
use PHPUnit\Framework\TestCase;
use PonyCool\Core\Jwt\Jwt as JwtLib;
use ReflectionException;

/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2022/12/28
 * Time: 5:04 PM
 */
class JwtTest extends TestCase
{
    private string $secret;

    protected function setUp(): void
    {
        parent::setUp();
        $this->secret = 'lrGeo1kWYY6ZM98A';
    }

    /**
     * @test
     * @return string|null
     */
    public function getJwtToken(): string|null
    {
        $secret = $this->secret;
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];
        $payload = [
            'sub' => 'test',
            'uid' => 1,
            'username' => 'test',
            'admin' => true,
            'account_id' => '2',
        ];
        $jwt = new JwtLib();
        try {
            $token = $jwt->getToken($secret, $header, $payload);
            $this->assertIsString($token);
        } catch (ReflectionException|Exception $e) {
            self::assertEquals(0, $e->getCode(), $e->getMessage());
            self::assertEmpty($e->getMessage(), $e->getMessage());
            return null;
        }
        return $token;
    }

    /**
     * 校验 JWT Token
     * @test
     * @depends getJwtToken
     * @param string $token
     * @return void
     * @throws Exception
     */
    public function testVerify(string $token)
    {
        $secret = $this->secret;
        $jwt = new JwtLib();
        try {
            $res = $jwt->verify($secret, $token);
            self::assertTrue($res, 'JWT Token 校验结果未达到预期，预期结果为true');
        } catch (Exception $e) {
            self::assertEmpty($e->getMessage(), 'JWT Token 校验结果未达到预期，校验过程中发生异常');
        }
    }
}