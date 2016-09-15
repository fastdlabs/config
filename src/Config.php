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

/**
 * Class Config
 *
 * @package FastD\Config
 */
class Config
{
    /**
     * @var array
     */
    protected $bag = [];

    /**
     * @var ConfigVariable
     */
    protected $variable;

    /**
     * @var ConfigCache
     */
    protected $caching;

    /**
     * Config constructor.
     *
     * @param string $cache
     */
    public function __construct($cache = null)
    {
        $this->variable = new ConfigVariable();

        $this->cache = new ConfigCache($cache);

        if ($this->) {}
    }

    /**
     * @param null $resource
     * @param bool $merge
     * @return array
     */
    public function load($resource = null, $merge = true)
    {
        $config = ConfigLoader::load($resource);

        if (is_array($config) && $merge) {
            $this->merge($config);
        }

        return $config;
    }

    /**
     * @param array $env
     * @return array
     */
    public function env(array $env)
    {
        $config = ConfigLoader::loadEnv($env);

        $this->merge($config);

        return $config;
    }

    /**
     * @param array $bag
     * @return $this
     */
    public function merge(array $bag = array())
    {
        if (empty($bag)) {
            return $this;
        }

        $merge = function ($array1, $array2) use (&$merge) {
            foreach ($array2 as $key => $value) {
                if (array_key_exists($key, $array1) && is_array($value)) {
                    $array1[$key] = $merge($array1[$key], $array2[$key]);
                } else {
                    $array1[$key] = $value;
                }
            }

            return $array1;
        };

        $this->bag = $merge($this->bag, $bag);

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
        return $this->bag;
    }

    /**
     * @param $name
     * @param $value
     * @return Config
     */
    public function set($name, $value)
    {
        $this->bag[$name] = $value;

        return $this;
    }

    /**
     * @param $name
     * @param $default
     * @return array
     */
    public function get($name, $default = null)
    {
        try {
            if (array_key_exists($name, $this->bag)) {
                return $this->bag[$name];
            }

            if (false === strpos($name, '.')) {
                throw new ConfigUndefinedException($name);
            }

            $keys = explode('.', $name);
            $parameters = $this->bag;

            foreach ($keys as $value) {
                if (!array_key_exists($value, $parameters)) {
                    throw new ConfigUndefinedException($name);
                }

                $parameters = $parameters[$value];
            }
            return $parameters;
        } catch (ConfigException $e) {
            return $default;
        }
    }

    public function __destruct()
    {
        if ($this->cache->isWritable()) {
            $this->cache->saveCache($this->all());
        }
    }
}