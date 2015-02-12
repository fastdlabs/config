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

use Dobee\Configuration\ConfigLoaderInterface;

abstract class LoaderAbstract implements ConfigLoaderInterface
{
    private $parameters = array();

    public function __construct($resource = null)
    {
        $this->load($resource);
    }

    public function load($resource = null)
    {
        if (!file_exists($resource)) {
            throw new ConfigurationFileNotFoundException(sprintf('%s\' is not found.', $resource));
        }

        return $this->setParameters($this->parser($resource));
    }

    public function setParameters(array $parameters = array())
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function getParameters($name = null)
    {
        return $this->parameters;
    }
 }