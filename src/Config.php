<?php
declare(strict_types=1);
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2018
 *
 * @link      https://www.github.com/fastdlabs
 * @link      https://fastdlabs.com/
 */

namespace FastD\Config;


use ArrayObject;
use LogicException;
use Throwable;

/**
 * Class Config.
 */
class Config extends ArrayObject
{
    const GLUE = '%';

    /**
     * @var array
     */
    protected array $variables = [];

    /**
     * Config constructor.
     *
     * @param $config
     * @param array $variables
     */
    public function __construct(array $config = [], array $variables = [])
    {
        parent::__construct($config);

        $this->variables = $variables;
    }

    /**
     * @param $file
     *
     * @return array
     */
    public function load(string $file): array
    {
        $config = load($file);

        $this->merge($config);

        return $config;
    }

    /**
     * @param string $key
     * @param string $default
     *
     * @return mixed
     */
    public function get($key, $default = '')
    {
        try {
            if ($this->offsetExists($key)) {
                $value = $this->offsetGet($key);
                return is_string($value) ? replace($value, $this->variables) : $value;
            }

            if (false === strpos($key, '.')) {
                throw new LogicException(sprintf('Array Undefined key %s', $key));
            }

            $value = $this->getArrayCopy();
            $keys = explode('.', $key);
            foreach ($keys as $name) {
                if (!isset($value[$name])) {
                    throw new LogicException(sprintf('Array Undefined key %s', $key));
                }

                $value = $value[$name];
            }
            unset($keys, $key);
            return is_string($value) ? replace($value, $this->variables) : $value;
        } catch (Throwable $exception) {
            return $default;
        }
    }

    /**
     * @param $key
     * @return bool
     */
    public function has($key): bool
    {
        if ($this->offsetExists($key)) {
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

    /**
     * @param $key
     * @param $value
     * @return ArrayObject
     */
    public function set($key, $value): ArrayObject
    {
        if ($this->offsetExists($key)) {
            $this->offsetSet($key, $value);
            return $this;
        }
        $keys = explode('.', $key);
        $firstDimension = array_shift($keys);

        $data = [];
        if ($this->offsetExists($firstDimension)) {
            $data = $this->offsetGet($firstDimension);
            ! is_array($data) && $data = [$data];
        }

        $target = &$data;

        foreach ($keys as $key) {
            $target = &$target[$key];
        }
        $target = $value;

        $this->offsetSet($firstDimension, $data);
        return $this;
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return (array)$this;
    }

    /**
     * @param $array
     * @return $this
     */
    public function merge($array)
    {
        $merge = function ($array1, $array2) use (&$merge) {
            foreach ($array2 as $key => $value) {
                if (array_key_exists($key, $array1) && is_array($value)) {
                    if (is_array($array1[$key])) {
                        $array1[$key] = $merge($array1[$key], $value);
                    } else {
                        array_unshift($value, $array1[$key]);
                        $array1[$key] = $value;
                    }
                } else {
                    if (is_string($key)) {
                        $array1[$key] = $value;
                    } else {
                        $array1[] = $value;
                    }
                }
            }

            return $array1;
        };

        $this->exchangeArray($merge($this->getArrayCopy(), $array));

        unset($merge);

        return $this;
    }

}
