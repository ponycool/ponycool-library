<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/7/1
 * Time: 10:33 上午
 */
declare(strict_types=1);

namespace PonyCool\MQ\RabbitMQ;


use PonyCool\Mq\MessageInterface;

class Message implements MessageInterface
{
    protected string $vhost;
    protected ?string $exchange;
    protected ?string $queue;
    protected ?string $routingKey;
    protected ?array $header;
    protected ?array $properties;
    protected ?string $body;

    public function __construct()
    {
        $this->vhost = '/';
        $this->exchange = null;
        $this->queue = null;
        $this->routingKey = null;
        $this->header = [];
        $this->properties = [];
        $this->body = null;
    }

    /**
     * @return string
     */
    public function getVhost(): string
    {
        return $this->vhost;
    }

    /**
     * @param string $vhost
     * @return Message
     */
    public function setVhost(string $vhost): Message
    {
        $this->vhost = $vhost;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getExchange(): ?string
    {
        return $this->exchange;
    }

    /**
     * @param string|null $exchange
     * @return Message
     */
    public function setExchange(?string $exchange): Message
    {
        $this->exchange = $exchange;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getQueue(): ?string
    {
        return $this->queue;
    }

    /**
     * @param string|null $queue
     * @return Message
     */
    public function setQueue(?string $queue): Message
    {
        $this->queue = $queue;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRoutingKey(): ?string
    {
        return $this->routingKey;
    }

    /**
     * @param string|null $routingKey
     * @return Message
     */
    public function setRoutingKey(?string $routingKey): Message
    {
        $this->routingKey = $routingKey;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getHeader(): ?array
    {
        return $this->header;
    }

    /**
     * @param array|null $header
     * @return Message
     */
    public function setHeader(?array $header): Message
    {
        $this->header = $header;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getProperties(): ?array
    {
        return $this->properties;
    }

    /**
     * @param array|null $properties
     * @return Message
     */
    public function setProperties(?array $properties): Message
    {
        $this->properties = $properties;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @param string|null $body
     * @return Message
     */
    public function setBody(?string $body): Message
    {
        $this->body = $body;
        return $this;
    }
}