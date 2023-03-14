<?php

namespace Jwt;

use Exception;
use PHPUnit\Framework\TestCase;
use PonyCool\Core\Jwt\Jwt as JwtLib;
use ReflectionException;
use function PHPUnit\Framework\assertEquals;

/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2022/12/28
 * Time: 5:04 PM
 */
class JwtTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function getJwtToken(): void
    {
        $secret = "lrGeokWYY6ZM98A";
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];
        $payload = [
            'sub' => 'test',
            'name' => 'test',
            'uid' => 1,
            'aid' => 1,
            'gid' => 1
        ];
        $jwt = new JwtLib();
        try {
            $token = $jwt->getToken($secret, $header, $payload);
            $this->assertIsString($token);
        } catch (ReflectionException|Exception $exc) {
            assertEquals(0, $exc->getCode(), $exc->getMessage());
        }
    }
}