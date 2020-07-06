<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2018
 *
 * @link      https://www.github.com/fastdlabs
 * @link      https://fastdlabs.com/
 */

namespace FastD\Config;


use ArrayObject;

/**
 * Class Config.
 */
class Config extends ArrayObject
{
    protected const GLUE = '%';

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

        $this->append($config);

        return $config;
    }

    /**
     * @param $value
     *
     * @return string
     */
    protected function replace(string $value): string
    {
        if ('env' === substr($value, 0, 3)) {
            $env = substr($value, 4);

            return env($env);
        }

        if (empty($value) || false === strpos($value, '%')) {
            return $value;
        }

        return $this->variable($value);
    }

    /**
     * @param string $key
     * @param string $default
     *
     * @return mixed
     */
    public function get($key, ?string $default = null)
    {
        try {
            $value = $this->offsetGet($key);
            return is_string($value) ? $this->replace($value) : $value;
        } catch (\Exception $exception) {
            return $default;
        }
    }

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

    /**
     * @param $key
     * @return mixed
     */
    public function find($key)
    {
        if ($this->offsetExists($key)) {
            return $this->offsetGet($key);
        }

        if (false === strpos($key, '.')) {
            throw new \LogicException(sprintf('Array Undefined key %s', $key));
        }

        $value = $this->getArrayCopy();
        $keys = explode('.', $key);
        foreach ($keys as $name) {
            if ( ! isset($value[$name])) {
                throw new \LogicException(sprintf('Array Undefined key %s', $key));
            }

            $value = $value[$name];
        }
        unset($keys, $key);

        return $value;
    }


    /**
     * @param $key
     * @return bool
     */
    public function has($key): bool
    {
        try {
            $this->find($key);
        } catch (\Exception $exception) {
            return false;
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
            return parent::set($key, $value);
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

        return parent::set($firstDimension, $data);
    }

    /**
     * @param $variable
     *
     * @return string
     */
    protected function variable($variable)
    {
        return preg_replace_callback(sprintf('/%s(\w*\.*\w*)%s/', static::GLUE, static::GLUE), function ($match) {
            if ( ! $this->has($match[1])) {
                throw new \Exception($match[1]);
            }

            return $this->variables[$match[1]];
        }, $variable);
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return (array)$this;
    }
}
