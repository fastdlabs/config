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
     * @var bool
     */
    protected $loadCache = false;

    /**
     * Config constructor.
     *
     * @param array $config
     * @param string $cache
     */
    public function __construct(array $config = [], $cache = null)
    {
        $this->variable = new ConfigVariable();

        if (null !== $cache) {
            $this->caching = new ConfigCache($cache);
            if ($this->caching->isWritable()) {
                $this->bag = $this->caching->loadCache();
                $this->loadCache = true;
            }
        }

        if (!$this->isLoadCache()) {
            $this->bag = $this->replace($config);
        }
    }

    /**
     * @return bool
     */
    public function isLoadCache()
    {
        return $this->loadCache;
    }

    /**
     * @param null $resource
     * @param bool $merge
     * @return $this
     */
    public function load($resource = null, $merge = true)
    {
        $config = ConfigLoader::load($resource);

        if (is_array($config) && $merge) {
            $this->merge($config);
        }

        return $this;
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
                        $bag[$key] = ConfigLoader::loadEnv([$env])[$env];
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
     * @param array $bag
     * @return $this
     */
    public function merge(array $bag)
    {
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

        $this->bag = $merge($this->bag, $this->replace($bag));

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
        if ($this->caching instanceof ConfigCache) {
            if ($this->caching->isWritable()) {
                $this->caching->saveCache($this->all());
            }
        }
    }
}