<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/10/23
 * Time: 3:26 下午
 */
declare(strict_types=1);

namespace PonyCool\Sms;

use ReflectionClass;
use ReflectionException;

class SmsFactory
{
    /**
     * 短信消息工厂
     * @param string $className
     * @return object|null
     */
    public static function factory(string $className): ?object
    {
        try {
            $payChannel = new ReflectionClass(__NAMESPACE__ . '\\Channel\\' . ucfirst($className) . '\\Message');
            if (!$payChannel->isSubclassOf(__NAMESPACE__ . '\\SmsMessageInterface')) {
                throw new ReflectionException($className . "未实现短信消息接口类");
            }
            return $payChannel->newInstance();
        } catch (ReflectionException $exception) {
            log_message('error', '{channel}加载失败', ['channel' => $className,]);
            return null;
        }
    }
}