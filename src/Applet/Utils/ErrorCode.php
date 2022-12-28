<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/9/9
 * Time: 10:40 下午
 */
declare(strict_types=1);

namespace PonyCool\Applet\Utils;

/**
 * Class ErrorCode
 * @package PonyCool\Applet\Utils
 */
class ErrorCode
{
    public static int $OK = 0;
    // encodingAesKey 非法
    public static int $IllegalAesKey = -41001;
    // 无效的iv
    public static int $IllegalIv = -41002;
    // 解密后得到的buffer非法
    public static int $IllegalBuffer = -41003;
    // base64加密失败
    public static int $DecodeBase64Error = -41004;
    // 解密失败
    public static int $DecryptionFailed = -1;
}
