<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/1/30
 * Time: 下午11:33
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */

namespace FastD\Config;

/**
 * Class Configuration
 *
 * @package FastD\Configuration
 */
class Config
{
    /**
     * @var array
     */
    protected $parameters = array();

    /**
     * @var Variable
     */
    protected $variable;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->variable = new Variable();
    }

    /**
     * @param      $key
     * @param null $value
     * @return $this
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
     * @return void|true
     */
    public function load($resource = null)
    {
        $this->merge(Loader::load($resource));
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
     * @return $this
     */
    public function set($name, $value)
    {
        $this->parameters[$name] = $value;

        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function remove($name)
    {
        if ($this->has($name)) {
            unset($this->parameters[$name]);
        }

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