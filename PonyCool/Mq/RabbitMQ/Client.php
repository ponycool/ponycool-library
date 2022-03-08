<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/6/30
 * Time: 11:06 上午
 */
declare(strict_types=1);

namespace PonyCool\MQ\RabbitMQ;

use Exception;
use CodeIgniter\CLI\CLI;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PonyCool\Mq\{Mq, Config, MessageInterface};
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class Client extends Mq
{
    private AMQPStreamConnection $conn;

    public function check(Config $config): bool
    {
        if (is_null($config->getHost()) || is_null($config->getUser() || is_null($config->getPass()))) {
            return false;
        }

        $this->config = $config;

        if ($config->getPort() === 0) {
            $this->config->setPort(5672);
        }
        return true;
    }

    /**
     * @return AMQPStreamConnection
     */
    public function getConn(): AMQPStreamConnection
    {
        return $this->conn;
    }

    /**
     * @param AMQPStreamConnection $conn
     * @return Mq
     */
    public function setConn(AMQPStreamConnection $conn): Mq
    {
        $this->conn = $conn;
        return $this;
    }


    /**
     * 发布订阅消息
     * @throws Exception
     */
    public function publish(MessageInterface $message): bool
    {
        try {
            if (!class_exists(AMQPMessage::class)) {
                throw new Exception('缺少依赖php-amqplib');
            }
            if (empty($message->getQueue())) {
                throw new Exception('未正确设置队列');
            }
            if (empty($message->getExchange())) {
                throw new Exception('未正确设置交换器');
            }
            $this->connection($message->getVhost());
            $conn = $this->getConn();
            log_message('info', 'RabbitMQ 发布者打开连接');
            $channel = $conn->channel();
            $channel->queue_declare(
                $message->getQueue(),
                false,
                true,
                false,
                false
            );
            $channel->exchange_declare(
                $message->getExchange(),
                AMQPExchangeType::TOPIC,
                true,
                true,
                false
            );
            $channel->queue_bind($message->getQueue(), $message->getExchange(), $message->getRoutingKey());
            $AMQPMessage = new AMQPMessage(
                $message->getBody(),
                $message->getProperties(),
            );
            if (count($message->getHeader())) {
                $AMQPMessage->set(
                    'application_headers',
                    new AMQPTable($message->getHeader())
                );
            }
            $channel->basic_publish($AMQPMessage, $message->getExchange(), $message->getRoutingKey());

            $channel->close();

            // 关闭连接
            $this->close();
            return true;
        } catch (Exception $e) {
            throw new Exception(sprintf('RabbitMQ发布消息失败，error：%s', $e->getMessage()));
        }
    }

    /**
     * 消费订阅消息，消费者必须支持常驻内存调用
     * @param MessageInterface $message
     * @param object|null $callback
     */
    public function consume(MessageInterface $message, ?object $callback = null): void
    {
        try {
            if (!class_exists(AMQPMessage::class)) {
                throw new Exception('缺少依赖php-amqplib');
            }
            if (empty($message->getQueue())) {
                throw new Exception('未正确设置队列');
            }
            if (empty($message->getExchange())) {
                throw new Exception('未正确设置交换器');
            }
            // 设置默认回调函数
            if (is_null($callback)) {
                $callback = function ($message) {
                    CLI::write('RabbitMQ 消费消息' . $message->body, 'yellow');
                    $message->ack();

                    log_message('info', 'RabbitMQ 消费消息：{msg}', ['msg' => $message->body]);
                    // Send a message with the string "quit" to cancel the consumer.
                    if ($message->body === 'quit') {
                        $message->getChannel()->basic_cancel($message->getConsumerTag());
                    }
                };
            }

            $this->connection($message->getVhost());
            $conn = $this->getConn();
            log_message('info', 'RabbitMQ 消费者打开连接');
            CLI::write('RabbitMQ 消费者打开连接', 'yellow');

            $channel = $conn->channel();
            $channel->queue_declare(
                $message->getQueue(),
                false,
                true,
                false,
                false
            );
            $channel->exchange_declare(
                $message->getExchange(),
                AMQPExchangeType::TOPIC,
                true,
                true,
                false
            );
            $channel->queue_bind($message->getQueue(), $message->getExchange(), $message->getRoutingKey());
            $consumerTag = 'consumer';
            $AMQPMessage = new AMQPMessage(
                $message->getBody(),
                $message->getProperties(),
            );
            if (count($message->getHeader())) {
                $AMQPMessage->set(
                    'application_headers',
                    new AMQPTable($message->getHeader())
                );
            }

            $channel->basic_consume($message->getQueue(), $consumerTag, false, false, false, false, $callback);

            // 关闭连接
            $shutdown = function ($channel, $conn) {
                $channel->close();
                $conn->close();
                log_message('info', 'RabbitMQ连接正常关闭');
            };

            // 注册中止时执行的函数
            register_shutdown_function($shutdown, $channel, $conn);

            while ($channel->is_consuming()) {
                $channel->wait();
            }

        } catch (Exception $e) {
            $err = sprintf('Rabbit MQ消费消息失败，error：%s', $e->getMessage());
            log_message('error', 'RabbitMQ消费消息失败，error：{error}',
                [
                    'error' => $err
                ]);
            CLI::error($err);
        }
    }

    /**
     * 连接RabbitMQ
     * @throws Exception
     */
    private function connection(string $vhost): void
    {

        try {
            $conn = new AMQPStreamConnection(
                $this->config->getHost(),
                (string)$this->config->getPort(),
                $this->config->getUser(),
                $this->config->getPass(),
                $vhost,
            );
            $this->setConn($conn);
        } catch (Exception $e) {
            throw new Exception(sprintf('Rabbit MQ连接异常，error：%s', $e->getMessage()));
        }
    }

    /**
     * 关闭连接
     * @throws Exception
     */
    private function close(): void
    {
        $conn = $this->getConn();
        try {
            $conn->close();
        } catch (Exception $e) {
            throw new Exception(sprintf('Rabbit MQ关闭连接异常，info：%s', $e->getMessage()));
        }
    }
}