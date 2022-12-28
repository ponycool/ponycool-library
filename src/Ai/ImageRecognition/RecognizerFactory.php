<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/5/25
 * Time: 4:52 下午
 */
declare(strict_types=1);

namespace PonyCool\Ai\ImageRecognition;


use Exception;
use ReflectionClass;
use ReflectionException;

class RecognizerFactory
{
    /**
     * 图像识别工厂
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
            if (!$class->isSubclassOf(__NAMESPACE__ . '\\RecognizerInterface')) {
                throw new ReflectionException($config->getSource() . "未实现图像识别接口类");
            }
            $recognizer = $class->newInstance();
            if ($recognizer->checkConfig($config) !== true) {
                throw new Exception('未通过配置检查，请检查配置');
            }
            return $recognizer;
        } catch (ReflectionException | Exception $e) {
            log_message('error', '图像识别源{ocr}加载失败，error：{error}',
                [
                    'recognizer' => $config->getSource(),
                    'error' => $e->getMessage()
                ]
            );
            return null;
        }
    }
}