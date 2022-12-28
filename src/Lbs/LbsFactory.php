<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/6/14
 * Time: 22:40
 */
declare(strict_types=1);

namespace PonyCool\Lbs;


use Exception;
use ReflectionClass;
use ReflectionException;

class LbsFactory
{
    /**
     * Lbs工厂
     * @param object $config
     * @return object|null
     */
    public static function factory(object $config): ?object
    {
        try {
            if (is_null($config->getSource())) {
                throw new Exception('未正确配置有效的源');
            }
            $class = new ReflectionClass(__NAMESPACE__ . '\\' . ucfirst($config->getSource() . '\\Lbs'));
            if (!$class->isSubclassOf(__NAMESPACE__ . '\\LbsInterface')) {
                throw new ReflectionException($config->getSource() . "未实现LBS接口类");
            }
            $lbs = $class->newInstance();
            if ($lbs->checkConfig($config) !== true) {
                throw new Exception('未通过配置检查，请检查配置');
            }
            return $lbs;
        } catch (ReflectionException | Exception $e) {
            log_message('error', 'LBS源{source}加载失败，error：{error}',
                [
                    'source' => $config->getSource(),
                    'error' => $e->getMessage()
                ]
            );
            return null;
        }
    }
}