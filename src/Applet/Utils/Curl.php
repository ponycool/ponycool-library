<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/9/8
 * Time: 10:29 下午
 */
declare(strict_types=1);

namespace PonyCool\Applet\Utils;

use CURLFile;

class Curl
{
    /**
     * 提交请求
     * @param string $url 请求的地址
     * @param null $data 提交的内容
     * @param string $method 提交的方法
     * @param string|null $ssl_cer 证书Cer路径
     * @param string|null $ssl_key 证书Key路径
     * @return bool|string
     */
    public static function do(string $url, $data = null, string $method = 'GET', ?string $ssl_cer = null, ?string $ssl_key = null): bool|string
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        // 在发起连接前等待的时间
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        // 设置cURL允许执行的最长秒数
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // 启用时会将服务器服务器返回的"Location: "放在header中递归的返回给服务器。
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        // 限定递归返回的数量
        curl_setopt($curl, CURLOPT_MAXREDIRS, 2);
        // POST请求
        if ($method === 'POST') {
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_POST, true);
            if (!is_null($data)) {
                $buildParams = function ($data) {
                    if (is_array($data)) {
                        foreach ($data as &$value) {
                            if (is_string($value) && $value[0] === '@' && class_exists('CURLFile', false)) {
                                $filename = realpath(trim($value, '@'));
                                file_exists($filename) && $value = new CURLFile($filename);
                            }
                        }
                    }
                    return $data;
                };

                $params = call_user_func($buildParams, $data);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
            }
        } else {
            curl_setopt($curl, CURLOPT_SSLVERSION, 1);
        }
        // 设置SSL证书
        if (!is_null($ssl_cer) && file_exists($ssl_cer) && is_file($ssl_cer)) {
            curl_setopt($curl, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($curl, CURLOPT_SSLCERT, $ssl_cer);
        }
        if (!is_null($ssl_key) && file_exists($ssl_key) && is_file($ssl_key)) {
            curl_setopt($curl, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($curl, CURLOPT_SSLKEY, $ssl_key);
        }

        list($content, $status) = array(curl_exec($curl), curl_getinfo($curl));
        curl_close($curl);
        return (intval($status["http_code"]) === 200) ? $content : false;
    }
}
