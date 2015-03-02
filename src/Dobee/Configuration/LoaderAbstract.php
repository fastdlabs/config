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

namespace Dobee\Configuration;

/**
 * Class LoaderAbstract
 *
 * @package Dobee\Configuration
 */
abstract class LoaderAbstract implements ConfigLoaderInterface
{
    /**
     * @var array
     */
    private $parameters = array();

    /**
     * @param null $resource
     * @throws ConfigurationFileNotFoundException
     */
    public function __construct($resource = null)
    {
        $this->load($resource);
    }

    /**
     * @param null $resource
     * @return LoaderAbstract
     * @throws ConfigurationFileNotFoundException
     */
    public function load($resource = null)
    {
        if (!file_exists($resource)) {
            throw new ConfigurationFileNotFoundException(sprintf('%s\' is not found.', $resource));
        }

        return $this->setParameters($this->parser($resource));
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
        return $this->parameters;
    }
 }