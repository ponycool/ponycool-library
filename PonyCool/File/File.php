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
        } catch (Exception | GuzzleException $e) {
            return false;
        }
    }
}