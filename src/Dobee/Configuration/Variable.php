<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/2/12
 * Time: 下午11:41
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */

namespace Dobee\Configuration;

class Variable 
{
    private $variable = array();

    public function setVariable($key, $value)
    {
        $this->variable[$key] = $value;

        return $this;
    }

    public function hasVariable($key)
    {
        return isset($this->variable[$key]);
    }

    public function getVariable($key)
    {
        return isset($this->variable[$key]) ? $this->variable[$key] : false;
    }
}