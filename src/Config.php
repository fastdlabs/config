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
    /**
     * @var Variable
     */
    protected $variable;

    /**
     * Config constructor.
     * @param array $config
     * @param array $variable
     */
    public function __construct(array $config = [], array $variable = [])
    {
        parent::__construct($config);

        $this->variable = new Variable($variable);
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

        return $this->variable->replace($value);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function find($key)
    {
        $value = parent::find($key);

        if (is_string($value)) {
            return $this->replace($value);
        }

        $replace = function ($bag) use (&$replace) {
            foreach ($bag as $key => $value) {
                if (is_array($value)) {
                    $replace($value);
                } else if (is_string($value)) {
                    $bar[$key] = $this->replace($value);
                }
            }

            return $bag;
        };

        return $replace($value);
    }

    /**
     * @param      $key
     * @param null $value
     * @return Config
     */
    public function setVariable($key, $value = null)
    {
        $this->variable->set($key, $value);

        return $this;
    }

    /**
     * @param $key
     * @return string
     */
    public function getVariable($key)
    {
        return $this->variable->find($key);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->data;
    }
}