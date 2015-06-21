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

use FastD\Config\Loader\IniFileLoader;
use FastD\Config\Loader\PhpFileLoader;
use FastD\Config\Loader\YmlFileLoader;

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
        $this->variable->setVariable($key, $value);

        return $this;
    }

    /**
     * @param $key
     * @return string
     */
    public function getVariable($key)
    {
        return $this->variable->getVariable($key);
    }

    /**
     * @param null $resource
     * @return void|true
     */
    public function load($resource = null)
    {
        switch(pathinfo($resource, PATHINFO_EXTENSION)) {
            case "ini":
                $this->addLoader(new IniFileLoader($resource));
                break;
            case "yml":
            case "yaml":
                $this->addLoader(new YmlFileLoader($resource));
                break;
            default:
                $this->addLoader(new PhpFileLoader($resource));
        }
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->parameters[$name]);
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function add($name, $value)
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
     * @param array $parameters
     * @return $this
     */
    public function merge(array $parameters = array())
    {
        $this->parameters = array_merge($this->parameters, $parameters);

        return $this;
    }

    /**
     * @param array $parameters
     * @return $this
     */
    public function set(array $parameters = array())
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @param null $name
     * @return array
     */
    public function get($name)
    {
        if (isset($this->parameters[$name])) {
            if (is_array($this->parameters[$name])) {
                return $this->parameters[$name];
            }

            return $this->variable->replaceVariable($this->parameters[$name]);
        }

        if (false === strpos($name, '.')) {
            throw new \InvalidArgumentException(sprintf('"%s" is undefined.', $name));
        }

        $keys = explode('.', $name);
        $parameters = $this->parameters;

        foreach ($keys as $value) {
            if (!isset($parameters[$value])) {
                throw new \InvalidArgumentException(sprintf('"%s" is undefined.', $name));
            }

            $parameters = $parameters[$value];
        }

        if (is_array($parameters)) {
            return $parameters;
        }

        return $this->variable->replaceVariable($parameters);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->parameters;
    }

    /**
     * @param Loader $loader
     * @return $this
     */
    public function addLoader(Loader $loader)
    {
        $this->merge($loader->getparameters());

        return $this;
    }
}