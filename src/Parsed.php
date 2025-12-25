<?php
declare(strict_types=1);

namespace FastD\Config;

class Parsed extends \ArrayObject
{
    public function get(string $key, mixed $default = null): mixed
    {
        if (parent::offsetExists($key)) {
            return parent::offsetGet($key);
        }

        if (!str_contains($key, '.')) {
            return $default;
        }

        $value = $this->getArrayCopy();
        $keys = explode('.', $key);

        foreach ($keys as $name) {
            if (!isset($value[$name])) {
                return $default;
            }

            $value = $value[$name];
        }

        return $value;
    }

    public function set(string $key, mixed $value): void
    {
        if (parent::offsetExists($key)) {
            parent::offsetSet($key, $value);
            return;
        }

        $keys = explode('.', $key);
        $firstDimension = array_shift($keys);

        $data = parent::offsetExists($firstDimension) ? parent::offsetGet($firstDimension) : [];

        if (!is_array($data)) {
            $data = [$data];
        }

        $target = &$data;

        foreach ($keys as $key) {
            $target = &$target[$key];
        }

        $target = $value;

        parent::offsetGet($firstDimension, $data);
    }

    public function has(string $key): bool
    {
        if (parent::offsetExists($key)) {
            return true;
        }

        $value = $this->getArrayCopy();
        $keys = explode('.', $key);

        foreach ($keys as $name) {
            if (!isset($value[$name])) {
                return false;
            }
            $value = $value[$name];
        }

        return true;
    }

    public function offsetGet($key): mixed
    {
        return $this->get($key, null);
    }

    public function offsetSet($key, $value): void
    {
        $this->set($key, $value);
    }

    public function offsetExists($key): bool
    {
        return $this->has($key);
    }
}