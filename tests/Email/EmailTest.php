<?php
/**
 * Created By PhpStorm
 * User: Pony
 * Data: 2024/8/9
 * Time: 上午10:49
 */
declare(strict_types=1);

namespace Email;

use PHPUnit\Framework\TestCase;
use PonyCool\Email\Mail;
use PonyCool\Email\Recipient;
use PonyCool\Email\Sender;
use PonyCool\Email\Server;

class EmailTest extends TestCase
{
    /**
     * 发送邮件
     * @return void
     */
    public function testSendMail()
    {
        $server = new Server('smtp.example.com',
            'username',
            'password',
        );
        $sender = new Sender('sender@example.com', 'Sender Name');
        $recipient = new Recipient('test@example.com', 'test');
        $recipients = [$recipient];
        $content = <<<HTML
<html>
<head>
    <title>测试邮件</title>
</head>
<body>
    <p>测试邮件</p>
</body>
</html>
HTML;
        $mail = new Mail($server, $sender, $recipients, '测试邮件', $content);
        $res = $mail->send();
        $this->assertTrue($res);
    }
}