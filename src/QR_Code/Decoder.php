<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/7/1
 * Time: 1:57 下午
 */
declare(strict_types=1);

namespace PonyCool\QR_Code;

use Exception;
use Zxing\QrReader;

class Decoder
{
    /**
     * 二维码反解析
     * @throws Exception
     */
    public static function decode(string $qrImg): string
    {
        if (!class_exists(QrReader::class)) {
            throw new Exception('未安装二维码反解析依赖：khanamiryan/qrcode-detector-decoder');
        }
        $qrcode = new QrReader($qrImg);
        return $qrcode->text();
    }
}