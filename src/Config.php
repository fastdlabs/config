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
use Exception;

/**
 * Class Config.
 */
class Config extends ArrayObject
{
    /**
     * @var array
     */
    protected array $variables = [];

    /**
     * Config constructor.
     *
     * @param array $config
     * @param array $variables
     * @throws Exception
     */
    public function __construct(array $config = [], array $variables = [])
    {
        parent::__construct($this->replace($config));

        $this->variables = $variables;
    }

    /**
     * @param $array
     * @return mixed
     */
    public function replace($array)
    {
        array_walk_recursive($array, function (&$value) {
            if (is_string($value)) {
                $value = preg_replace_callback('/%([^%]+)%/', function ($matches) {
                    $keys = explode('.', $matches[1]);
                    $currentValue = $this->variables;

                    foreach ($keys as $key) {
                        if (isset($currentValue[$key])) {
                            $currentValue = $currentValue[$key];
                        } else {
                            return $matches[0];
                        }
                    }

                    return $currentValue;
                }, $value);
            }
        });

        return $array;
    }

    /**
     * @param string $file
     *
     * @return array
     * @throws Exception
     */
    public function load(string $file): array
    {
        $config = $this->replace(load($file));

        $this->add($config);

        return $config;
    }

    /**
     * @param string $key
     * @param $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        if ($this->offsetExists($key)) {
            return $this->offsetGet($key);
        }

        if (false === strpos($key, '.')) {
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
        unset($keys, $key);
        return $value;
    }

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

    public function set($key, $value): void
    {
        if ($this->offsetExists($key)) {
            $this->offsetSet($key, $value);
            return;
        }
        $keys = explode('.', $key);
        $firstDimension = array_shift($keys);

        $data = [];
        if ($this->offsetExists($firstDimension)) {
            $data = $this->offsetGet($firstDimension);
            !is_array($data) && $data = [$data];
        }

        $target = &$data;

        foreach ($keys as $key) {
            $target = &$target[$key];
        }
        $target = $value;

        $this->offsetSet($firstDimension, $data);
    }

    /**
     * @param $array
     * @return void
     * @throws Exception
     */
    public function add($array): void
    {
        $config = array_replace_recursive($this->getArrayCopy(), $array);

        $this->exchangeArray($config);
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return (array)$this;
    }
}
