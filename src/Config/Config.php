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
    protected $parameters = [];

    /**
     * @var ConfigVariable
     */
    protected $variable;

    /**
     * @var ConfigCache
     */
    protected $cache;

    /**
     * Config constructor.
     *
     * @param null $cache
     */
    public function __construct($cache = null)
    {
        $this->variable = new ConfigVariable();
        
        $this->cache = new ConfigCache($this, $cache);
    }

    /**
     * @param null $resource
     * @param bool $merge
     * @return array|mixed
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
     * @return array
     */
    public function loadCache($cacheType = ConfigCache::SAVE_CACHE_PHP)
    {
        return $this->cache->loadCache($cacheType);
    }

    /**
     * @return bool
     */
    public function saveCache()
    {
        return $this->cache->saveCacheFile();
    }

    /**
     * @param array $parameters
     * @return $this
     */
    public function merge(array $parameters = array())
    {
        if (empty($this->parameters)) {
            $this->parameters = $parameters;
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

        $this->parameters = $merge($this->parameters, $parameters);

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
        return $this->parameters;
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        try {
            $this->get($name);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $name
     * @param $value
     * @return Config
     */
    public function set($name, $value)
    {
        $this->parameters[$name] = $value;

        return $this;
    }

    /**
     * @param $name
     * @param $default
     * @return array
     */
    public function hasGet($name, $default)
    {
        try {
            return $this->get($name);
        } catch (\Exception $e) {
            return $default;
        }
    }

    /**
     *
     *
     * @param null $name
     * @return array
     */
    public function get($name)
    {
        if (array_key_exists($name, $this->parameters)) {
            if (is_array($this->parameters[$name])) {
                return $this->parameters[$name];
            }

            return $this->variable->replace($this->parameters[$name]);
        }

        if (false === strpos($name, '.')) {
            throw new \InvalidArgumentException(sprintf('"%s" is undefined.', $name));
        }

        $keys = explode('.', $name);
        $parameters = $this->parameters;

        foreach ($keys as $value) {
            if (!array_key_exists($value, $parameters)) {
                throw new \InvalidArgumentException(sprintf('"%s" is undefined.', $name));
            }

            $parameters = $parameters[$value];
        }

        return is_array($parameters) ? $parameters : $this->variable->replace($parameters);
    }
}