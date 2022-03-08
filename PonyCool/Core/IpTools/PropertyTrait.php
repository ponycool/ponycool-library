<?php
declare(strict_types=1);

namespace PonyCool\Core\IpTools;


trait PropertyTrait
{
    public function __get(string $name)
    {
        if (method_exists($this, $name)) {
            return $this->$name();
        }
        foreach (array('get', 'to') as $prefix) {
            $method = $prefix . ucfirst($name);
            if (method_exists($this, $method)) {
                return $this->$method();
            }
        }
        trigger_error('Undefined property');
        return null;
    }

    public function __set(string $name, string $value): void
    {
        $method = 'set' . ucfirst($name);
        if (!method_exists($this, $method)) {
            trigger_error('Undefined property');
            return;
        }
        $this->$method($value);
    }
}