<?php
declare(strict_types=1);

namespace PonyCool\Core\Jwt\Validation;

use ReflectionClass;
use ReflectionException;

class ValidationStrategy extends Strategy
{
    /**
     * @return string
     */
    public function getStrategy(): string
    {
        return $this->strategy;
    }

    /**
     * @param string $strategy
     */
    public function setStrategy(string $strategy): void
    {
        $this->strategy = $strategy;
    }

    /**
     * @return object
     */
    public function getValidationStrategy(): object
    {
        return $this->validationStrategy;
    }

    /**
     * @param object $validationStrategy
     */
    public function setValidationStrategy(object $validationStrategy): void
    {
        $this->validationStrategy = $validationStrategy;
    }

    /**
     * 策略验证器
     * @param string $param
     * @return bool
     * @throws ReflectionException
     */
    public function validator(string $param): bool
    {
        try {
            $strategyReflection = new ReflectionClass(__NAMESPACE__ . '\\Strategy\\' . ucfirst($this->strategy));
            if (!$strategyReflection->isSubclassOf(__NAMESPACE__ . '\\Strategy\\StrategyInterface')) {
                throw new ReflectionException($this->strategy . "验证策略未实现验证策略接口");
            }
            $validationStrategy = $strategyReflection->newInstance();
            $this->setValidationStrategy($validationStrategy);
        } catch (ReflectionException $exception) {
            throw new ReflectionException($this->strategy . "验证策略加载失败");
        }
        $result = $this->getValidationStrategy()->validator($param);
        return (bool)$result;
    }
}
