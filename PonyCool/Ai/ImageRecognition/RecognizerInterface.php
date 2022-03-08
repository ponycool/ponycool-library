<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/5/25
 * Time: 4:49 下午
 */
declare(strict_types=1);

namespace PonyCool\Ai\ImageRecognition;


interface RecognizerInterface
{

    /**
     * 检查配置
     * @param Config $config
     * @return bool
     */
    public function checkConfig(Config $config): bool;

    /**
     * 汽车识别
     * @return mixed
     */
    public function recognizeCar(): array;

}