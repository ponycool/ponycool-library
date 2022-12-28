<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/6/30
 * Time: 11:00 上午
 */
declare(strict_types=1);

namespace PonyCool\Mq;


abstract class Mq implements MqInterface
{
    // 配置
    protected Config $config;

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @param Config $config
     * @return Mq
     */
    public function setConfig(Config $config): Mq
    {
        $this->config = $config;
        return $this;
    }
}