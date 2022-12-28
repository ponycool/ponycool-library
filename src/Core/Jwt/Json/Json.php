<?php
declare(strict_types=1);

namespace PonyCool\Core\Jwt\Json;


use DomainException;

class Json
{
    /**
     * JSON 编码
     * @param array $input
     * @return false|string
     */
    public static function jsonEncode(array $input): bool|string
    {
        $json = json_encode($input);
        if (function_exists('json_last_error') && $errno = json_last_error()) {
            static::handleJsonError($errno);
        } elseif ($json === 'null') {
            throw new DomainException('输入为non-null并且结果为Null');
        }
        return $json;
    }

    /**
     * JSON 解码
     * @param $input
     * @return mixed
     */
    public static function jsonDecode($input): array
    {
        if (version_compare(PHP_VERSION, '5.4.0', '>=') && !(defined('JSON_C_VERSION') && PHP_INT_SIZE > 4)) {
            /** In PHP >=5.4.0, json_decode() accepts an options' parameter, that allows you
             * to specify that large ints (like Steam Transaction IDs) should be treated as
             * strings, rather than the PHP default behaviour of converting them to floats.
             */
            $arr = json_decode($input, true, 512, JSON_BIGINT_AS_STRING);
        } else {
            /** Not all servers will support that, however, so for older versions we must
             * manually detect large ints in the JSON string and quote them (thus converting
             *them to strings) before decoding, hence the preg_replace() call.
             */
            $max_int_length = strlen((string)PHP_INT_MAX) - 1;
            $json_without_bigints = preg_replace('/:\s*(-?\d{' . $max_int_length . ',})/', ': "$1"', $input);
            $arr = json_decode($json_without_bigints, true);
        }
        if (function_exists('json_last_error') && $errno = json_last_error()) {
            static::handleJsonError($errno);
        } elseif ($arr === null && $input !== 'null') {
            throw new DomainException('输入为non-null并且结果为Null');
        }
        return $arr;
    }

    /**
     * JSON错误处理
     * @param $errno
     */
    protected static function handleJsonError($errno)
    {
        $messages = array(
            JSON_ERROR_DEPTH => '超出最大堆栈深度',
            JSON_ERROR_STATE_MISMATCH => 'JSON无效或格式错误',
            JSON_ERROR_CTRL_CHAR => '控制字符错误',
            JSON_ERROR_SYNTAX => '语法错误，格式错误的JSON',
            JSON_ERROR_UTF8 => '格式错误的UTF-8字符' //PHP >= 5.3.3
        );
        throw new DomainException(
            $messages[$errno] ?? 'Unknown JSON error: ' . $errno
        );
    }
}
