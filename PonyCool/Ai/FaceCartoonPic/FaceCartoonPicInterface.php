<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/5/25
 * Time: 4:49 下午
 */
declare(strict_types=1);

namespace PonyCool\Ai\FaceCartoonPic;


use PonyCool\Ai\Config\Conf;

interface FaceCartoonPicInterface
{

    /**
     * 配置检查
     * @param Conf $config
     * @return bool
     */
    public function check(Conf $config): bool;

    /**
     * 人像动漫化
     * @return array
     */
    public function faceCartoonPic(): array;
}