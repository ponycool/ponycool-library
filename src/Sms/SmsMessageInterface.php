<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/10/23
 * Time: 3:29 下午
 */
declare(strict_types=1);

namespace PonyCool\Sms;

interface SmsMessageInterface
{
    /**
     * 发送短信
     * @param array $phoneNumbers
     * @param string $templateId 模板ID
     * @param array|null $templateParam 模板参数
     * @return array
     */
    public function send(array $phoneNumbers, string $templateId, ?array $templateParam = null): array;
}