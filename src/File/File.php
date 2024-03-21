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
        } catch (Exception|GuzzleException) {
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
        // 用于记录文件内查找的当前位置
        $pos = -2;
        // 初始化eof变量，用于存储查找过程中的结束标识符（默认是换行符'\n'）
        $eof = "";
        $str = "";
        $resArray = [];
        // 定位到文件末尾
        fseek($fp, 0, SEEK_END);
        while ($n > 0) {
            while ($eof != "\n") {
                if (!fseek($fp, $pos, SEEK_END)) {
                    $eof = fgetc($fp);
                    $pos--;
                } else {
                    if ($n == 1) {
                        $pos++;
                        fseek($fp, $pos, SEEK_END);
                    }
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
        fclose($fp);

        $resArray = array_reverse($resArray);

        return match ($returnType) {
            'json' => json_encode($resArray),
            'array' => $resArray,
            default => $str,
        };
    }

    /**
     * 分页获取目录下的文件
     * @param string $dir 目录
     * @param int $page 页码
     * @param int $size
     * @return array
     */
    public static function paginateFiles(string $dir, int $page = 1, int $size = 10): array
    {
        // 获取所有文件
        $files = glob($dir . '/*.*', GLOB_BRACE);

        // 计算总页数
        $totalPages = ceil(count($files) / $size);

        // 偏移量
        $offset = ($page - 1) * $size;

        // 获取当前页的文件
        $currentPageFiles = array_slice($files, $offset, $size);

        // 返回结果
        return [
            'page' => $page,
            'size' => $size,
            'total' => count($files),
            'totalPages' => $totalPages,
            'currentPageFiles' => $currentPageFiles,
        ];
    }
}