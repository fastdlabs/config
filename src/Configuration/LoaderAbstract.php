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
    private $options = array();

    public function __construct($resource = null)
    {
        if (null !== $resource && method_exists($this, 'load')) {
            $this->load($resource);
        }
    }

    public function setOptions(array $options = array())
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions($name = null)
    {
        return $this->options;
    }
 }