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
 * Class Config
 *
 * @package FastD\Config
 */
class Config extends ArrayObject
{
    const GLUE = '%';

    /**
     * @var array
     */
    protected $variables = [];

    /**
     * Config constructor.
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
     * @return $this
     */
    public function load($file)
    {
        $config = load($file);

        $this->merge($config);

        return $config;
    }

    /**
     * @param $value
     * @return string
     */
    protected function replace($value)
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
     * @param $key
     * @param null $default
     * @return mixed|null|string
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

    /**
     * @param $variable
     * @return string
     */
    protected function variable($variable)
    {
        return preg_replace_callback(sprintf('/%s(\w*\.*\w*)%s/', static::GLUE, static::GLUE), function ($match) {
            if (!$this->has($match[1])) {
                throw new \Exception($match[1]);
            }
            return $this->variables[$match[1]];
        }, $variable);
    }

    /**
     * @return array
     */
    public function all()
    {
        return (array) $this;
    }
}