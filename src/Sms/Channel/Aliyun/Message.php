<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/10/24
 * Time: 2:34 下午
 */
declare(strict_types=1);

namespace PonyCool\Sms\Channel\Aliyun;

use PonyCool\Sms\SmsMessageInterface;

class Message implements SmsMessageInterface
{

    public function send(array $phoneNumbers, string $templateId, ?array $templateParam = null): array
    {
        // TODO: Implement send() method.
    }
}