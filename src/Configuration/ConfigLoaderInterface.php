<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/2/4
 * Time: 下午7:11
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */

namespace Dobee\Configuration;

interface ConfigLoaderInterface extends ParserInterface
{
    public function load($resource = null);

    public function getOptions($name = null);
}