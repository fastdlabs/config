<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/1/30
 * Time: 下午10:17
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */

namespace Dobee\Configuration;

interface ParserInterface
{
    public function parser($resource = null);

    public function setOptions(array $options = null);

    /**
     * @return array
     */
    public function getOptions();
}