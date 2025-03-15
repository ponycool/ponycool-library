<?php
/**
 * Created By PhpStorm
 * User: Pony
 * Data: 2025/3/15
 * Time: 16:28
 */
declare(strict_types=1);

namespace PonyCool\Core\Util;

class System
{
    /**
     * 判断是否在Docker容器中
     * @return bool
     */
    public static function inDocker(): bool
    {
        return file_exists('/.dockerenv') || file_exists('/run/.containerenv');
    }
}