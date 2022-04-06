<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/5/25
 * Time: 4:52 下午
 */
declare(strict_types=1);

namespace PonyCool\Ai\SwapGenderPic;


use Exception;
use ReflectionClass;
use ReflectionException;

class SwapGenderPicFactory
{
    /**
     * 人脸性别转换工厂
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
            if (!$class->isSubclassOf(__NAMESPACE__ . '\\SwapGenderPicInterface')) {
                throw new ReflectionException($config->getSource() . "未实现人脸性别转换AI接口类");
            }
            $swapGender = $class->newInstance();
            if ($swapGender->check($config) !== true) {
                throw new Exception('未通过配置检查，请检查配置');
            }
            return $swapGender;
        } catch (ReflectionException | Exception $e) {
            log_message('error', '人脸性别转换AI源{source}加载失败，error：{error}',
                [
                    'source' => $config->getSource(),
                    'error' => $e->getMessage()
                ]
            );
            return null;
        }
    }
}