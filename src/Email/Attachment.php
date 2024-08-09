<?php
/**
 * Created By PhpStorm
 * User: Pony
 * Data: 2024/8/9
 * Time: 上午9:58
 */
declare(strict_types=1);

namespace PonyCool\Email;

use PHPMailer\PHPMailer\PHPMailer;

class Attachment
{
    protected string $path;
    protected string $name;
    // 编码
    protected string $encoding;
    // 类型
    protected string $type;
    // 描述
    protected string $disposition;

    public function __construct(string $path, string $name = '',
                                string $encoding = PHPMailer::ENCODING_BASE64,
                                string $type = '',
                                string $disposition = 'attachment')
    {
        $this->path = $path;
        $this->name = $name;
        $this->encoding = $encoding;
        $this->type = $type;
        $this->disposition = $disposition;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): Attachment
    {
        $this->path = $path;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Attachment
    {
        $this->name = $name;
        return $this;
    }

    public function getEncoding(): string
    {
        return $this->encoding;
    }

    public function setEncoding(string $encoding): Attachment
    {
        $this->encoding = $encoding;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): Attachment
    {
        $this->type = $type;
        return $this;
    }

    public function getDisposition(): string
    {
        return $this->disposition;
    }

    public function setDisposition(string $disposition): Attachment
    {
        $this->disposition = $disposition;
        return $this;
    }


}