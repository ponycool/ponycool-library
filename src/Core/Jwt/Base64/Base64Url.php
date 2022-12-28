<?php
declare(strict_types=1);

namespace PonyCool\Core\Jwt\Base64;

class Base64Url
{
    /**
     * Base64 URL 编码
     * @param string $input
     * @return string
     */
    public static function base64UrlEncode(string $input): string
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

    /**
     * Base64 URL 解码
     * @param string $input
     * @return bool|string
     */
    public static function base64UrlDecode(string $input): bool|string
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }
}
