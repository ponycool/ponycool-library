<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/6/30
 * Time: 10:28 上午
 */
declare(strict_types=1);

namespace PonyCool\Mq;

interface MqInterface
{
    /**
     * 配置检查
     * @param Config $config
     * @return bool
     */
    public function check(Config $config): bool;


    /**
     * 发布订阅消息
     * @param MessageInterface $message
     * @return bool
     */
    public function publish(MessageInterface $message): bool;

    /**
     * 消费订阅消息，消费者必须支持常驻内存调用
     * @param MessageInterface $message
     * @param object|null $callback
     */
    public function consume(MessageInterface $message, ?object $callback = null): void;
}

