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
     * @param null $resource
     * @return array|mixed
     */
    public function load($resource = null)
    {
        $config = ConfigLoader::load($resource);
        
        if (is_array($config)) {
            $this->merge($config);    
        }

        return $config;
    }

    /**
     * @param null $cache
     */
    public function loadCache($cache = null)
    {
        
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
     * @return bool
     */
    public function remove($name)
    {
        if ($this->has($name)) {
            unset($this->parameters[$name]);
        }

        return true;
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

        if (is_array($parameters)) {
            return $parameters;
        }

        return $this->variable->replace($parameters);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     * @return $this
     */
    public function merge(array $parameters = array())
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

        $this->parameters = $merge($this->parameters, $parameters);

        return $this;
    }
}