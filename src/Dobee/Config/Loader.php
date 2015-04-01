<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/1/30
 * Time: 下午10:15
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */

namespace Dobee\Config;

/**
 * Class Loader
 *
 * @package Dobee\Configuration
 */
abstract class Loader
{
    /**
     * @var array
     */
    private $parameters = array();

    /**
     * @param null $resource
     */
    public function __construct($resource = null)
    {
        if (null !== $resource) {
            $this->load($resource);
        }
    }

    /**
     * @param null $resource
     * @return mixed
     */
    public function load($resource = null)
    {
        if (empty($resource)) {
            throw new \InvalidArgumentException('Configuration resource is empty or null.');
        }

        if (!file_exists($resource)) {
            throw new \LogicException(sprintf('Configuration resource file "%s" is not found.', $resource));
        }

        $this->parameters = $this->parse($resource);
    }

    /**
     * @param null $resource
     * @return mixed
     */
    abstract public function parse($resource = null);

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
     * @throw InvalidArgumentException
     */
    public function getParameters($name = null)
    {
        if (null === $name) {
            return $this->parameters;
        }

        if (!isset($this->parameters[$name])) {
            throw new \InvalidArgumentException(sprintf('Configuration "%s" is undefined.', $name));
        }

        return $this->parameters[$name];
    }
 }