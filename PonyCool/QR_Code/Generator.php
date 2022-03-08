<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/9/27
 * Time: 3:15 下午
 */
declare(strict_types=1);

namespace PonyCool\QR_Code;

include('qrlib.php');

use Exception;

class Generator
{
    // 保存路径
    private string $savePath;
    // 二维码内容
    private string $codeText;
    // 二维码文件名
    private string $fileName;
    /**
     * @var string
     * 纠错级别 默认L [ 'L','M','Q','H' ]
     * 纠错级别越高，生成图片会越大。
     * L 水平 7%的字码可被修正
     * M 水平 15%的字码可被修正
     * Q 水平 25%的字码可被修正
     * H 水平 30%的字码可被修正
     */
    private string $ecLevel;
    // 二维码图片大小，默认是3
    private int $size;
    // 二维码周围边框空白区域间距值，默认是4
    private int $margin;
    // Logo
    private ?string $logo;

    public function __construct()
    {
        $this->setEcLevel('L')
            ->setSize(3)
            ->setMargin(4)
            ->setLogo(null);
    }

    /**
     * @return string
     */
    public function getSavePath(): string
    {
        return $this->savePath;
    }

    /**
     * @param string $savePath
     * @return $this
     */
    public function setSavePath(string $savePath): Generator
    {
        $this->savePath = $savePath;
        return $this;
    }

    /**
     * @return string
     */
    public function getCodeText(): string
    {
        return $this->codeText;
    }

    /**
     * @param string $codeText
     * @return $this
     */
    public function setCodeText(string $codeText): Generator
    {
        $this->codeText = $codeText;
        return $this;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     * @return $this
     */
    public function setFileName(string $fileName): Generator
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * @return string
     */
    public function getEcLevel(): string
    {
        return $this->ecLevel;
    }

    /**
     * @param string $ecLevel
     * @return $this
     */
    public function setEcLevel(string $ecLevel): Generator
    {
        $this->ecLevel = $ecLevel;
        return $this;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @param int $size
     * @return $this
     */
    public function setSize(int $size): Generator
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return int
     */
    public function getMargin(): int
    {
        return $this->margin;
    }

    /**
     * @param int $margin
     * @return $this
     */
    public function setMargin(int $margin): Generator
    {
        $this->margin = $margin;
        return $this;
    }

    /**
     * @return string
     */
    public function getLogo(): ?string
    {
        return $this->logo;
    }

    /**
     * @param string|null $logo
     * @return $this
     */
    public function setLogo(?string $logo): Generator
    {
        $this->logo = $logo;
        return $this;
    }


    /**
     * 生成二维码
     * @return bool
     */
    public function generate(): bool
    {
        if (empty($this->savePath)) {
            return false;
        }
        try {
            if (!is_dir($this->savePath)) {
                $res = mkdir(
                    iconv("UTF-8", "GBK", $this->savePath),
                    0777, true
                );
                if (!$res) {
                    throw new Exception('生成二维码时，创建保存目录失败');
                }
            }
            if (empty($this->fileName)) {
                throw new Exception('生成二维码时，文件名无效');
            }
            if (empty($this->codeText)) {
                throw new Exception('缺少二维码内容');
            }
            if (!in_array($this->ecLevel, ['L', 'M', 'Q', 'H'])) {
                throw new Exception('纠错级别无效');
            }
            $path = $this->savePath . '/' . $this->fileName;
            if (!file_exists($path)) {
                \QRcode::png(
                    $this->codeText,
                    $path,
                    $this->ecLevel,
                    $this->size,
                    $this->margin
                );
            }

            // Logo
            if (!is_null($this->logo)) {
                $qrCode = imagecreatefromstring(file_get_contents($path));
                if (!file_exists($this->logo)) {
                    return false;
                }
                list($qrCodeWidth, $qrCodeHeight) = getimagesize($path);
                $logo = imagecreatefromstring(file_get_contents($this->logo));
                list($logoSrcWidth, $logoSrcHeight) = getimagesize($this->logo);
                // logo缩放比率
                $ratio = 0.3;
                $logoWidth = intval($qrCodeWidth * $ratio);
                $logoHeight = intval($qrCodeHeight * $ratio);
                $logoX = intval(($qrCodeWidth - $logoWidth) / 2);
                $logoY = intval(($qrCodeHeight - $logoHeight) / 2);
                // 重新组合图片
                imagecopyresampled(
                    $qrCode,
                    $logo,
                    $logoX,
                    $logoY,
                    0,
                    0,
                    $logoWidth,
                    $logoHeight,
                    $logoSrcWidth,
                    $logoSrcHeight,
                );
                imagepng($qrCode, $path);
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
