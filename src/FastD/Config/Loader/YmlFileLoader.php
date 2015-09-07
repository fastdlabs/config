<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/1/30
 * Time: 下午10:16
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */

namespace FastD\Config\Loader;

use FastD\Config\Loader;
use Symfony\Component\Yaml\Yaml;

class YmlFileLoader extends Loader
{
    /**
     * @param null $resource
     * @return mixed
     */
    public function parse($resource = null)
    {
        return Yaml::parse($resource);
    }
}