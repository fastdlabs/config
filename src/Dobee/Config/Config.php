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

namespace Dobee\Config;

use Dobee\Config\Loader\IniFileLoader;
use Dobee\Config\Loader\PhpFileLoader;
use Dobee\Config\Loader\YmlFileLoader;

/**
 * Class Configuration
 *
 * @package Dobee\Configuration
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
    public function hasParameters($name)
    {
        return isset($this->parameters[$name]);
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function addParameters($name, $value)
    {
        $this->parameters[$name] = $value;

        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function removeParameters($name)
    {
        if ($this->hasparameters($name)) {
            unset($this->parameters[$name]);
        }

        return $this;
    }

    /**
     * @param array $parameters
     * @return $this
     */
    public function mergeParameters(array $parameters = array())
    {
        $this->parameters = array_merge($this->parameters, $parameters);

        return $this;
    }

    /**
     * @param array $parameters
     * @return $this
     */
    public function setParameters(array $parameters = array())
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @param null $name
     * @return array
     */
    public function getParameters($name = null)
    {
        if (null === $name) {
            return $this->parameters;
        }

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
     * @param Loader $loader
     * @return $this
     */
    public function addLoader(Loader $loader)
    {
        $this->mergeParameters($loader->getparameters());

        return $this;
    }
}