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

class Configuration 
{
    private $options = array();

    private static $instance;

    private $loaders = array();

    private function __construct(array $resource = null) {}

    public static function createConfigurationLoader(array $resource = null)
    {
        if (null === self::$instance) {
            self::$instance = new self($resource);
        }

        return self::$instance;
    }

    public function hasOptions($name)
    {
        return isset($this->options[$name]);
    }

    public function addOptions($name, $value)
    {
        $this->options[$name] = $value;

        return $this;
    }

    public function removeOptions($name)
    {
        if ($this->hasOptions($name)) {
            unset($this->options[$name]);
        }

        return $this;
    }

    public function mergeOptions(array $options = array())
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    public function setOptions(array $options = array())
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions($name = null)
    {
        if (null === $name) {
            return $this->options;
        }

        $keys = explode('.', $name);
        $options = $this->options;

        foreach ($keys as $value) {
            if (!isset($options[$value])) {
                throw new \InvalidArgumentException(sprintf('%s\' is undefined.', $name));
            }
            $options = $options[$value];
        }

        return $options;
    }

    public function addLoader(ConfigLoaderInterface $loaderInterface)
    {
        $this->loaders[] = $loaderInterface;

        $this->mergeOptions($loaderInterface->getOptions());

        return $this;
    }
}