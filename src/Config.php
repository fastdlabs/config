<?php
/**
 *
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Config;

use FastD\Config\Exceptions\ConfigException;
use FastD\Config\Exceptions\ConfigUndefinedException;
use FastD\Utils\Arr;

/**
 * Class Config
 *
 * @package FastD\Config
 */
class Config
{
    /**
     * @var Arr
     */
    protected $config = [];

    /**
     * @var ConfigVariable
     */
    protected $variable;

    /**
     * Config constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = arr($config);

        $this->variable = new ConfigVariable();
    }

    /**
     * @param null $resource
     * @return $this
     */
    public function load($resource = null)
    {
        $config = load($resource);

        if (is_array($config)) {
            $this->merge($config);
        }

        return $config;
    }

    /**
     * @param array $bag
     * @return array
     */
    protected function replace(array $bag)
    {
        $replace = function ($bag) use (&$replace) {
            foreach ($bag as $key => $value) {
                if (is_array($value)) {
                    $replace($value);
                } else if (is_string($value)) {
                    if ('env' === substr($value, 0, 3)) {
                        $env = substr($value, 4);
                        $bag[$key] = env([$env])[$env];
                    } else {
                        $bag[$key] = $this->variable->replace($value);
                    }
                }
            }

            return $bag;
        };

        return $replace($bag);
    }

    /**
     * @param array $array
     * @return $this
     */
    public function merge(array $array)
    {
        $this->config->merge($array);

        return $this;
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
        return $this->variable->get($key);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->config->toRaw();
    }
}