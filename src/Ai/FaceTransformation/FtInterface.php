<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/5/25
 * Time: 4:49 下午
 */
declare(strict_types=1);

namespace PonyCool\Ai\FaceTransformation;


use PonyCool\Ai\Config\Conf;

interface FtInterface
{

    /**
     * 配置检查
     * @param Conf $config
     * @return bool
     */
    public function check(Conf $config): bool;

    /**
     * 人脸年龄变化
     * @param int $age 变化到的人脸年龄 [10,80]
     * @return array
     */
    public function changeAge(int $age): array;
}