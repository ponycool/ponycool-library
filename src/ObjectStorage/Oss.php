<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/9/24
 * Time: 1:39 下午
 */
declare(strict_types=1);

namespace PonyCool\ObjectStorage;

use OSS\OssClient;
use OSS\Core\OssException;

class Oss implements ObjectStorageInterface
{
    private ObjectStorage $os;

    public function __construct(ObjectStorage $objectStorage)
    {
        $this->os = $objectStorage;
    }

    /**
     * 上传文件
     * @param string $filePath 文件原始路径包含文件名称
     * @param string $osPath OSS保存路径包含文件名称
     * @return string|null OSS文件地址
     */
    public function upload(string $filePath, string $osPath): ?string
    {
        try {
            $ossClient = new OssClient($this->os->getAccessKey(), $this->os->getSecret(), $this->os->getRegion());
            $ossClient->uploadFile($this->os->getBucket(), $osPath, $filePath);
            if (is_null($this->os->getDomain())) {
                return str_replace('oss', $this->os->getBucket() . '.oss', $this->os->getRegion()) . '/' . $osPath;
            }
            return $this->os->getDomain() . '/' . $osPath;
        } catch (OssException $e) {
            log_message('error', 'OSS上传文件失败，error：{error}', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * 下载文件
     * @param string $file OS文件名包含OS文件夹
     * @param string $savePath OS保存路径包含文件名称
     * @return bool
     */
    public function download(string $file, string $savePath): bool
    {
        try {
            $options = [
                OssClient::OSS_FILE_DOWNLOAD => $savePath
            ];
            $ossClient = new OssClient($this->os->getAccessKey(), $this->os->getSecret(), $this->os->getRegion());
            $ossClient->getObject($this->os->getBucket(), $file, $options);
            return true;
        } catch (OssException $e) {
            log_message('error', 'OSS下载文件失败，error：{error}', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
