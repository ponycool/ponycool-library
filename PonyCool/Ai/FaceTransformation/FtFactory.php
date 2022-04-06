<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/5/25
 * Time: 4:52 下午
 */
declare(strict_types=1);

namespace PonyCool\Ai\FaceTransformation;


use Exception;
use ReflectionClass;
use ReflectionException;

class FtFactory
{
    /**
     * 人脸变换工厂
     * @param object $config
     * @return object|null
     */
    public static function factory(object $config): ?object
    {
        try {
            if (is_null($config->getSource())) {
                throw new Exception('未正确配置有效的源');
            }
            $class = new ReflectionClass(__NAMESPACE__ . '\\' . ucfirst($config->getSource()));
            if (!$class->isSubclassOf(__NAMESPACE__ . '\\FtInterface')) {
                throw new ReflectionException($config->getSource() . "未实现人像变换AI接口类");
            }
            $ft = $class->newInstance();
            if ($ft->check($config) !== true) {
                throw new Exception('未通过配置检查，请检查配置');
            }
            return $ft;
        } catch (ReflectionException | Exception $e) {
            log_message('error', '人像变换AI源{source}加载失败，error：{error}',
                [
                    'source' => $config->getSource(),
                    'error' => $e->getMessage()
                ]
            );
            return null;
        }
    }
}