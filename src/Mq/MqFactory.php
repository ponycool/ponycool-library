<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/6/30
 * Time: 10:45 上午
 */
declare(strict_types=1);

namespace PonyCool\Mq;


use Exception;
use ReflectionClass;
use ReflectionException;

class MqFactory
{
    /**
     * 消息队列工厂
     * @param object $config
     * @return object|null
     */
    public static function factory(object $config): ?object
    {
        try {
            if (is_null($config->getDriver())) {
                throw new Exception('未正确配置有效的驱动');
            }
            $class = new ReflectionClass(__NAMESPACE__ . '\\' . ucfirst($config->getDriver()) . 'MQ' . '\\Client');
            if (!$class->isSubclassOf(__NAMESPACE__ . '\\MqInterface')) {
                throw new ReflectionException($config->getDriver() . "未实现消息队列接口类");
            }
            $mq = $class->newInstance();
            if ($mq->check($config) !== true) {
                throw new Exception('未通过配置检查，请检查配置');
            }
            return $mq;
        } catch (Exception | ReflectionException $e) {
            log_message('error', '消息队列源{source}加载失败，error：{error}',
                [
                    'source' => $config->getSource(),
                    'error' => $e->getMessage()
                ]
            );
            return null;
        }
    }
}