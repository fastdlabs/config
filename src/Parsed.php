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

        foreach ($keys as $key) {
            if (!isset($value[$key])) {
                return $default;
            }

            $value = $value[$key];
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

        parent::offsetSet($firstDimension, $data);
    }

    public function has(string $key): bool
    {
        if (parent::offsetExists($key)) {
            return true;
        }

        $value = $this->getArrayCopy();
        $keys = explode('.', $key);

        foreach ($keys as $key) {
            if (!isset($value[$key])) {
                return false;
            }
            $value = $value[$key];
        }

        return true;
    }

    public function unset(string $key): void
    {
        if (!str_contains($key, '.')) {
            parent::offsetUnset($key);
            return;
        }

        $keys = explode('.', $key);
        $firstDimension = array_shift($keys);

        // 检查第一层是否存在
        if (!parent::offsetExists($firstDimension)) {
            return;
        }

        $data = parent::offsetGet($firstDimension);
        if (!is_array($data)) {
            parent::offsetUnset($firstDimension);
            return;
        }

        $target = &$data;
        $pathExists = true;

        for ($i = 0; $i < count($keys) - 1; $i++) {
            $segment = $keys[$i];
            if (!isset($target[$segment]) || !is_array($target[$segment])) {
                $pathExists = false;
                break;
            }
            $target = &$target[$segment];
        }

        if ($pathExists) {
            $lastSegment = $keys[count($keys) - 1];
            unset($target[$lastSegment]);
        }

        parent::offsetSet($firstDimension, $data);
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

    public function offsetUnset($key): void
    {
        $this->unset($key);
    }
}