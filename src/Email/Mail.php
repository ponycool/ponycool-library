<?php
/**
 * Created By PhpStorm
 * User: Pony
 * Data: 2024/8/8
 * Time: 下午4:33
 */
declare(strict_types=1);

namespace PonyCool\Email;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Mail
{
    // 邮件服务器
    protected Server $server;
    // 发件人
    protected Sender $sender;
    // 收件人列表
    protected array $recipients = [];
    protected ?ReplyTo $replyTo;
    // 抄送
    protected ?array $cc;
    // 密送
    protected ?array $bcc;
    protected ?array $attachments;

    // 将电子邮件格式设置为HTML
    protected bool $isHTML;

    protected string $subject;
    // 包含 HTML 格式的邮件正文
    protected ?string $content;
    // 包含纯文本版本的邮件正文
    protected ?string $textContent;

    // 是否启用调试模式
    protected bool $debug;

    public function __construct(Server   $server, Sender $sender, array $recipients,
                                string   $subject, ?string $content = null, ?string $textContent = null,
                                ?ReplyTo $replyTo = null, ?array $cc = null, ?array $bcc = null,
                                ?array   $attachments = null, bool $isHTML = true, bool $debug = false)
    {
        $this->server = $server;
        $this->sender = $sender;
        $this->recipients = $recipients;
        $this->replyTo = $replyTo;
        $this->cc = $cc;
        $this->bcc = $bcc;
        $this->attachments = $attachments;
        $this->isHTML = $isHTML;
        $this->subject = $subject;
        $this->content = $content;
        $this->textContent = $textContent;
        $this->debug = $debug;
    }

    public function getServer(): Server
    {
        return $this->server;
    }

    public function setServer(Server $server): Mail
    {
        $this->server = $server;
        return $this;
    }

    public function getSender(): Sender
    {
        return $this->sender;
    }

    public function setSender(Sender $sender): Mail
    {
        $this->sender = $sender;
        return $this;
    }

    public function getRecipients(): array
    {
        return $this->recipients;
    }

    public function setRecipients(array $recipients): Mail
    {
        $this->recipients = $recipients;
        return $this;
    }

    public function getReplyTo(): ?ReplyTo
    {
        return $this->replyTo;
    }

    public function setReplyTo(?ReplyTo $replyTo): Mail
    {
        $this->replyTo = $replyTo;
        return $this;
    }

    public function getCc(): ?array
    {
        return $this->cc;
    }

    public function setCc(?array $cc): Mail
    {
        $this->cc = $cc;
        return $this;
    }

    public function getBcc(): ?array
    {
        return $this->bcc;
    }

    public function setBcc(?array $bcc): Mail
    {
        $this->bcc = $bcc;
        return $this;
    }

    public function getAttachments(): ?array
    {
        return $this->attachments;
    }

    public function setAttachments(?array $attachments): Mail
    {
        $this->attachments = $attachments;
        return $this;
    }

    public function isHTML(): bool
    {
        return $this->isHTML;
    }

    public function setIsHTML(bool $isHTML): Mail
    {
        $this->isHTML = $isHTML;
        return $this;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): Mail
    {
        $this->subject = $subject;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): Mail
    {
        $this->content = $content;
        return $this;
    }

    public function getTextContent(): ?string
    {
        return $this->textContent;
    }

    public function setTextContent(?string $textContent): Mail
    {
        $this->textContent = $textContent;
        return $this;
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }

    public function setDebug(bool $debug): Mail
    {
        $this->debug = $debug;
        return $this;
    }


    /**
     * 发送邮件
     * @return bool|string
     */
    public function send(): bool|string
    {
        // 创建实例；传递“true”会启用异常
        $mail = new PHPMailer(true);

        try {
            // 服务器设置
            $server = $this->getServer();
            if ($server->isSMTP()) {
                $mail->isSMTP();
                $mail->SMTPAuth = $server->isSMTPAuth();
                $mail->SMTPSecure = $server->getSMTPSecure();
                $mail->SMTPDebug = $this->isDebug() ? SMTP::DEBUG_SERVER : SMTP::DEBUG_OFF;
            }
            $mail->Host = $server->getHost();
            $mail->Username = $server->getUsername();
            $mail->Password = $server->getPassword();
            $mail->Port = $server->getPort();

            // 设置收件人
            $sender = $this->getSender();
            $mail->setFrom($sender->getEmail(), $sender->getName());
            $recipients = $this->getRecipients();
            foreach ($recipients as $recipient) {
                if (!is_object($recipient) || !$recipient instanceof Recipient) {
                    throw new Exception('收件人必须为Recipient对象，且必须为Recipient类型');
                }
                $mail->addAddress($recipient->getEmail(), $recipient->getName());
            }
            if (is_null($this->getReplyTo())) {
                $mail->addReplyTo($sender->getEmail(), $sender->getName());
            } else {
                $replyTo = $this->getReplyTo();
                $mail->addAddress($replyTo->getEmail(), $replyTo->getName());
            }
            $cc = $this->getCc();
            if (!is_null($cc)) {
                foreach ($cc as $item) {
                    if (!is_object($item) || !$item instanceof Recipient) {
                        throw new Exception('抄送人必须为Recipient对象，且必须为Recipient类型');
                    }
                    $mail->addCC($item->getEmail(), $item->getName());
                }
            }
            $bcc = $this->getBcc();
            if (!is_null($bcc)) {
                foreach ($bcc as $item) {
                    if (!is_object($item) || !$item instanceof Recipient) {
                        throw new Exception('密送人必须为Recipient对象，且必须为Recipient类型');
                    }
                    $mail->addBCC($item->getEmail(), $item->getName());
                }
            }

            // 添加附件
            $attachments = $this->getAttachments();
            if (!is_null($attachments)) {
                foreach ($attachments as $attachment) {
                    if (!is_object($attachment) || !$attachment instanceof Attachment) {
                        throw new Exception('附件必须为Attachment对象，且必须为Attachment类型');
                    }
                    $mail->addAttachment($attachment->getPath(), $attachment->getName(),
                        $attachment->getEncoding(),
                        $attachment->getType(),
                        $attachment->getDisposition());
                }
            }

            $mail->Subject = $this->getSubject();
            if ($this->isHTML()) {
                $mail->isHTML();
                if (is_null($this->getContent())) {
                    throw new Exception('HTML邮件必须设置内容');
                }
                $mail->Body = $this->getContent();
            }

            if (!is_null($this->getTextContent())) {
                $mail->AltBody = $this->getTextContent();
            }

            $mail->CharSet = PHPMailer::CHARSET_UTF8;
            $mail->send();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return true;
    }
}