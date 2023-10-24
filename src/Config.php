<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Config;

use FastD\Utils\ArrayObject;

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
     * @param $value
     *
     * @return string
     */
    protected function replace($value): string
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
     * @param $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        try {
            $value = $this->find($key);
            return is_string($value) ? $this->replace($value) : $value;
        } catch (\Exception $exception) {
            return $default;
        }
    }

    public function has($key): bool
    {
        try {
            $this->find($key);
        } catch (\Exception $exception) {
            return false;
        }

        return true;
    }

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
    protected function variable(string $variable): string
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
