<?php
/**
 * Created by PhpStorm.
 * User: Pony
 * Date: 2020/10/15
 * Time: 2:34 下午
 */
declare(strict_types=1);

namespace PonyCool\Pay;

use ReflectionClass;
use ReflectionException;

class PayFactory
{
    /**
     * 支付工厂
     * @param string $className
     * @return object|null
     */
    public static function factory(string $className): ?object
    {
        try {
            $payChannel = new ReflectionClass(__NAMESPACE__ . '\\Channel\\' . ucfirst($className) . '\\Pay');
            if (!$payChannel->isSubclassOf(__NAMESPACE__ . '\\PayInterface')) {
                throw new ReflectionException($className . "未实现支付接口类");
            }
            return $payChannel->newInstance();
        } catch (ReflectionException $exception) {
            log_message('error', '{channel}加载失败', ['channel' => $className,]);
            return null;
        }
    }
}