<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/9/24
 * Time: 1:38 下午
 */
declare(strict_types=1);

namespace PonyCool\ObjectStorage;

interface ObjectStorageInterface
{

    /**
     * 初始化
     * ObjectStorageInterface constructor.
     * @param ObjectStorage $os
     */
    public function __construct(ObjectStorage $os);

    /**
     * 上传文件
     * @param string $filePath 文件原始路径包含文件名称
     * @param string $osPath OS保存路径包含文件名称
     * @return string|null OS文件地址
     */
    public function upload(string $filePath, string $osPath): ?string;

    /**
     * 下载文件
     * @param string $file OS文件名包含OS文件夹
     * @param string $savePath 保存路径，包含文件路径和文件名
     * @return bool
     */
    public function download(string $file, string $savePath): bool;

    /**
     * 获取直传签名
     * @return string
     */
    public function getSignature(): string;
}
