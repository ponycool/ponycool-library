<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/4/13
 * Time: 4:18 下午
 */
declare(strict_types=1);

namespace PonyCool\Cors;


class Header
{
    // 请求头
    protected array $headers = [];

    public function __construct()
    {
        foreach ($_SERVER as $name => $value) {
            if (str_starts_with($name, 'HTTP_')) {
                $this->headers[str_replace(' ', '-',
                    ucwords(
                        strtolower(
                            str_replace('_', ' ', substr($name, 5)
                            )
                        )
                    ))] = $value;
            }
        }
    }

    /**
     * 判断key是否存在header中
     * @param $key
     * @return bool
     */
    public function has($key): bool
    {
        if (array_key_exists($key, $this->headers)) {
            return true;
        }
        return false;
    }

    /**
     * @param $key
     * @return string
     */
    public function get($key): string
    {
        return $this->headers[$key];
    }
}