<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/6/11
 * Time: 4:35 下午
 */
declare(strict_types=1);

namespace PonyCool\Image;


class Img
{
    /**
     * 将背景图片和贴图合成一张图片
     * @param string $im 图像绝对路径，包含文件名
     * @param string $stamp 贴图
     * @param string $savePath 保存路径
     * @param int|null $width 贴图宽度，注意不是修改贴图宽度
     * @param int|null $height 贴图高度，注意不是修改贴图高度
     * @param int $x 贴图X坐标
     * @param int $y 贴图Y坐标
     * @return string|null
     */
    public static function merge(string $im, string $stamp, string $savePath, ?int $width = null, ?int $height = null, int $x = 0, int $y = 0): ?string
    {
        //创建图片对象

        $im = self::createImgFromPath($im);
        $stamp = self::createImgFromPath($stamp);

        if (is_null($width)) {
            $width = imagesx($stamp);
        }
        if (is_null($height)) {
            $height = imagesy($stamp);
        }
        //合成图片
        //将 src_im 图像中坐标从 src_x，src_y 开始，宽度为 src_w，高度为 src_h 的一部分拷贝到 dst_im 图像中坐标为 dst_x 和 dst_y 的位置上。
        //两图像将根据 pct 来决定合并程度，其值范围从 0 到 100。当 pct = 0 时，实际上什么也没做，当为 100 时对于调色板图像本函数和 imagecopy() 完全一样，
        //它对真彩色图像实现了 alpha 透明。
        imagecopymerge($im, $stamp, $x, $y, 0, 0, $width, $height, 100);

        // 输出合成图片
        list($t1, $t2) = explode(' ', microtime());
        $time = (int)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
        $mergeIm = sprintf('%s/merge_%s.png', $savePath, $time);
        $res = imagepng($im, $mergeIm);

        //释放内存
        imagedestroy($im);

        if ($res !== true) {
            return null;
        }
        return $mergeIm;
    }

    /**
     * 将背景图片设置透明，然后和贴图合成一张图片
     * @param string $im 图像绝对路径，包含文件名
     * @param string $stamp 贴图
     * @param string $savePath 保存路径
     * @param int|null $width 贴图宽度，注意不是修改贴图宽度
     * @param int|null $height 贴图高度，注意不是修改贴图高度
     * @param int $x 贴图X坐标
     * @param int $y 贴图Y坐标
     * @return string|null
     */
    public static function mergeTrueColor(string $im, string $stamp, string $savePath, ?int $width = null, ?int $height = null, int $x = 0, int $y = 0): ?string
    {
        //创建图片对象

        $im = self::createImgFromPath($im);
        $stamp = self::createImgFromPath($stamp);

        if (is_null($width)) {
            $width = imagesx($stamp);
        }
        if (is_null($height)) {
            $height = imagesy($stamp);
        }

        //创建真彩画布
        $imTrueColor = imageCreatetruecolor(imagesx($im), imagesy($im));

        //为真彩画布创建白色背景
        $color = imagecolorallocate($imTrueColor, 255, 255, 255);

        //在 image 图像的坐标 x，y（图像左上角为 0, 0）处用 color 颜色执行区域填充（即与 x, y 点颜色相同且相邻的点都会被填充）
        imagefill($imTrueColor, 0, 0, $color);

        //设置透明
        imageColorTransparent($imTrueColor, $color);

        //复制图片一到真彩画布中（重新取样-获取透明图片）

        imagecopyresampled($imTrueColor, $im, 0, 0, 0, 0, imagesx($im), imagesy($im), imagesx($im), imagesy($im));

        //合成图片
        //将 src_im 图像中坐标从 src_x，src_y 开始，宽度为 src_w，高度为 src_h 的一部分拷贝到 dst_im 图像中坐标为 dst_x 和 dst_y 的位置上。
        //两图像将根据 pct 来决定合并程度，其值范围从 0 到 100。当 pct = 0 时，实际上什么也没做，当为 100 时对于调色板图像本函数和 imagecopy() 完全一样，
        //它对真彩色图像实现了 alpha 透明。
        imagecopymerge($imTrueColor, $stamp, $x, $y, 0, 0, $width, $height, 100);

        // 输出合成图片
        list($t1, $t2) = explode(' ', microtime());
        $time = (int)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
        $mergeIm = sprintf('%s/merge_true_color_%s.png', $savePath, $time);
        $res = imagepng($imTrueColor, $mergeIm);

        //释放内存
        imagedestroy($im);
        imagedestroy($stamp);
        imagedestroy($imTrueColor);

        if ($res !== true) {
            return null;
        }
        return $mergeIm;
    }

    /**
     * 从图片路径创建图片资源
     * @param string $im
     * @return false|\GdImage|resource|null
     */
    public static function createImgFromPath(string $im)
    {
        $image = null;
        switch (self::getType($im)) {
            case 'PNG':
                $image = imagecreatefrompng($im);
                break;
            case 'JPG':
                $image = imagecreatefromjpeg($im);
                break;
            default:
                break;
        }
        return $image;
    }

    /**
     * 获取图像类型
     * @param string $im
     * @return string|null
     */
    public static function getType(string $im): ?string
    {
        $type = null;
        switch (getimagesize($im)[2]) {
            case 1:
                $type = 'GIF';
                break;
            case 2:
                $type = 'JPG';
                break;
            case 3:
                $type = 'PNG';
                break;
            case 4:
                $type = 'SWF';
                break;
            case 5:
                $type = 'PSD';
                break;
            case 6:
                $type = 'BMP';
                break;
            case 7:
                $type = 'TIFF(intel byte order)';
                break;
            case 8:
                $type = ' TIFF(motorola byte order)';
                break;
            case 9:
                $type = 'JPC';
                break;
            case 10:
                $type = 'JP2';
                break;
            case 11:
                $type = 'JPX';
                break;
            case 12:
                $type = 'JB2';
                break;
            case 13:
                $type = 'SWC';
                break;
            case 14:
                $type = 'IFF';
                break;
            case 15:
                $type = 'WBMP';
                break;
            case 16:
                $type = 'XBM';
                break;
            default:
                break;
        }
        return $type;
    }

    /**
     * 修改图片大小
     * @param string $im 图片路径
     * @param int $width 修改后的宽度
     * @param int $height 修改后的高度
     * @return bool
     */
    public static function resizeImage(string $im, int $width, int $height): bool
    {
        $type = self::getType($im);
        $im1 = null;
        if ($type === "JPG")
            $im1 = imagecreatefromjpeg($im);
        elseif ($type === "PNG")
            $im1 = imagecreatefrompng($im);
        elseif ($type === "GIF")
            $im1 = imagecreatefromgif($im);

        if (is_null($im1)) {
            return false;
        }

        $x = imagesx($im1);
        $y = imagesy($im1);

        if ($x >= $y) {
            $newX = $width;
            $newY = $newX * $y / $x;
        } else {
            $newY = $height;
            $newX = $x / $y * $newY;
        }

        $im2 = imagecreatetruecolor($newX, $newY);
        $res = imagecopyresized($im2, $im1, 0, 0, 0, 0, (int)floor($newX), (int)floor($newY), $x, $y);
        if ($res !== true) {
            return false;
        }
        // 保存图像并释放内存
        $res = imagepng($im2, $im);
        imagedestroy($im2);

        return $res;
    }
}