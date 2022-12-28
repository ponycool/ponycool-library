<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/6/29
 * Time: 1:16 下午
 */
declare(strict_types=1);

namespace PonyCool\Image;


class Base64
{
    /**
     * 图片Base64编码
     * @param $img
     * @return string|null
     */
    public static function encode($img): ?string
    {
        if (file_exists($img)) {

            // 图片大小，类型
            $imgInfo = getimagesize($img);
            // 图片可读权限
            $fp = fopen($img, "r");
            if ($fp) {
                $filesize = filesize($img);
                $content = fread($fp, $filesize);

                $content = chunk_split(base64_encode($content));
                switch ($imgInfo[2]) {
                    case 1:
                        $type = "gif";
                        break;
                    case 2:
                        $type = "jpg";
                        break;
                    case 3:
                        $type = "png";
                        break;
                    default:
                        return null;
                }

                // 合成图片的base64编码
                $base64 = 'data:image/' . $type . ';base64,' . $content;
            }

            fclose($fp);
        }
        return $base64 ?? null;
    }

    /**
     * 远程图片Base64编码
     * @param string $url
     * @return string|null
     */
    public static function encodeRemoteImg(string $url): ?string
    {

        $imgInfo = getimagesize($url);

        $content = chunk_split(base64_encode(file_get_contents($url)));
        switch ($imgInfo[2]) {
            case 1:
                $type = "gif";
                break;
            case 2:
                $type = "jpg";
                break;
            case 3:
                $type = "png";
                break;
            default:
                return null;
        }

        // 合成图片的base64编码
        return 'data:image/' . $type . ';base64,' . $content;
    }

    /**
     * 解码图片Base64数据
     * @param string $base64 base64字符串
     * @param string $savePath 保存路径
     * @param string $filename 文件名
     * @return string
     */
    public static function decode(string $base64, string $savePath, string $filename): string
    {
        $file = sprintf('%s/%s', $savePath, $filename);
        $base64 = explode(',', $base64);
        $data = base64_decode($base64[1]);
        file_put_contents($file, $data);
        return $file;
    }
}