<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/12/16
 * Time: 3:01 下午
 */
declare(strict_types=1);

namespace PonyCool\Log;

use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonoLogger;

class Logger
{
    public const DEBUG = MonoLogger::DEBUG;
    public const INFO = MonoLogger::INFO;
    public const NOTICE = MonoLogger::NOTICE;
    public const WARNING = MonoLogger::WARNING;
    public const ERROR = MonoLogger::ERROR;
    public const CRITICAL = MonoLogger::CRITICAL;
    public const ALERT = MonoLogger::ALERT;
    public const EMERGENCY = MonoLogger::EMERGENCY;


    /**
     * 记录日志
     * @param string $message 消息
     * @param array $context 内容
     * @param string $channel 频道
     * @param int $level 日志级别
     * @param string|null $logPath 日志路径
     */
    public static function log(string $message, array $context = [], string $channel = 'system',
                               int    $level = MonoLogger::DEBUG, ?string $logPath = null): void
    {
        if (is_null($logPath)) {
            return;
        }
        $log = new MonoLogger($channel);
        $log->pushHandler(new StreamHandler($logPath, $level));
        match ($level) {
            self::DEBUG => $log->debug($message, $context),
            self::INFO => $log->info($message, $context),
            self::NOTICE => $log->notice($message, $context),
            self::WARNING => $log->warning($message, $context),
            self::ERROR => $log->error($message, $context),
            self::CRITICAL => $log->critical($message, $context),
            self::ALERT => $log->alert($message, $context),
            self::EMERGENCY => $log->emergency($message, $context)
        };
    }
}