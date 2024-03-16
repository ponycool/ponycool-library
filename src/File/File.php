<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/6/28
 * Time: 2:20 下午
 */
declare(strict_types=1);

namespace PonyCool\File;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class File
{
    /**
     * 文件下载
     * @param string $remoteFileUrl 远程文件URL
     * @param string $savePath 保存路径
     * @param string|null $fileName 文件名
     * @return bool
     */
    public static function download(string $remoteFileUrl, string $savePath, ?string $fileName = null): bool
    {
        try {
            $conf = ['verify' => false];
            $client = new Client($conf);
            if (is_null($fileName)) {
                $temp = explode('/', $remoteFileUrl);
                $fileName = end($temp);
                $fileName .= '.jpg';
            }
            $filePath = sprintf('%s/%s', $savePath, $fileName);
            $response = $client->request('get', $remoteFileUrl, ['sink' => $filePath]);
            if ($response->getStatusCode() !== 200) {
                throw new Exception('网络请求失败');
            }
            if (!file_exists($filePath)) {
                throw new Exception('文件保存失败');
            }
            return true;
        } catch (Exception|GuzzleException $e) {
            return false;
        }
    }

    /**
     * 读取文件最后N行数据
     * @param string $file 文件路径
     * @param $n int 读取数据行数
     * @param string $returnType 返回数据类型
     * @return false|string|array
     */
    public static function getFileLastLines(string $file, int $n = 100, string $returnType = "array"): false|string|array
    {
        if (!file_exists($file)) {
            return false;
        }
        if (!$fp = fopen($file, 'r')) {
            return false;
        }
        $pos = -2;
        $eof = "";
        $str = "";
        $resArray = array();
        while ($n > 0) {
            while ($eof != "\n") {
                if (!fseek($fp, $pos, SEEK_END)) {
                    $eof = fgetc($fp);
                    $pos--;
                } else {
                    break;
                }
            }
            $line = fgets($fp);
            if ($line !== false) {
                $str .= $line;
                $resArray[] = $line;
            }
            $eof = "";
            $n--;
        }
        return match ($returnType) {
            'json' => json_encode($resArray),
            'array' => $resArray,
            default => $str,
        };
    }
}