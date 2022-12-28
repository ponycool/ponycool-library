<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/5/25
 * Time: 4:49 下午
 */
declare(strict_types=1);

namespace PonyCool\Ai\Ocr;


interface OcrInterface
{

    /**
     * 检查配置
     * @param Config $config
     * @return bool
     */
    public function checkConfig(Config $config): bool;

    /**
     * 通用印刷识别
     * @return mixed
     */
    public function generalBasicOCR(): array;

    /**
     * 通用手写体识别
     * @return array
     */
    public function generalHandwritingOCR(): array;


    /**
     * 汽车车牌识别
     * @return array
     */
    public function licensePlateOCR(): array;
}