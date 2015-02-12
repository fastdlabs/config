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

namespace Dobee\Configuration;

use Dobee\Configuration\Loader\IniFileLoader;
use Dobee\Configuration\Loader\PhpFileLoader;
use Dobee\Configuration\Loader\YamlFileLoader;

/**
 * Class Configuration
 *
 * @package Dobee\Configuration
 */
class Configuration
{
    /**
     * @var array
     */
    private $parameters = array();

    /**
     * @var $this
     */
    private static $instance;

    /**
     * @var Variable
     */
    private $variable;

    /**
     * @param array $resource
     */
    private function __construct(array $resource = null)
    {
        $this->variable = new Variable();
    }

    /**
     * @param array $resource
     * @return Configuration
     */
    public static function createConfigurationLoader(array $resource = null)
    {
        if (null === self::$instance) {
            self::$instance = new self($resource);
        }

        return self::$instance;
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

    public function getVariable($key)
    {
        return $this->variable->getVariable($key);
    }

    /**
     * @param null $resource
     */
    public function load($resource = null)
    {
        switch(pathinfo($resource, PATHINFO_EXTENSION)) {
            case "php":
                $loader = new PhpFileLoader($resource);
                break;
            case "ini":
                $loader = new IniFileLoader($resource);
                break;
            case "yml":
            case "yaml":
                $loader = new YamlFileLoader($resource);
        }

        $this->addLoader($loader);
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

        $keys = explode('.', $name);
        $parameters = $this->parameters;

        foreach ($keys as $value) {
            if (!isset($parameters[$value])) {
                throw new \InvalidArgumentException(sprintf('%s\' is undefined.', $name));
            }
            $parameters = $parameters[$value];
        }

        return $parameters;
    }

    /**
     * @param ConfigLoaderInterface $loaderInterface
     * @return $this
     */
    public function addLoader(ConfigLoaderInterface $loaderInterface)
    {
        $this->mergeparameters($loaderInterface->getparameters());

        return $this;
    }
}