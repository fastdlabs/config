<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/1/30
 * Time: 下午10:19
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */

namespace Dobee\Configuration\Loader;

use Dobee\Configuration\ConfigurationFileNotFoundException;
use Dobee\Configuration\LoaderAbstract;

class PhpFileLoader extends LoaderAbstract
{
    public function load($resource = null)
    {
        if (!file_exists($resource)) {
            throw new ConfigurationFileNotFoundException(sprintf('%s\' is not found.', $resource));
        }

        return $this->setOptions($this->parser($resource));
    }

    public function parser($resource = null)
    {
        return include $resource;
    }
}