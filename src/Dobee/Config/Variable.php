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

namespace Dobee\Config;

/**
 * Class Variable
 *
 * @package Dobee\Configuration
 */
class Variable
{
    /**
     * @var array
     */
    private $variable = array();

    /**
     * @var string
     */
    private $delimiter = '%';

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function setVariable($name, $value)
    {
        $this->variable[$name] = $value;

        return $this;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasVariable($name)
    {
        return isset($this->variable[$name]);
    }

    /**
     * @param $name
     * @return string
     */
    public function getVariable($name)
    {
        if (!$this->hasVariable($name)) {
            throw new \InvalidArgumentException(sprintf('Variable "%s" is undefined.', $name));
        }

        return $this->variable[$name];
    }

    /**
     * @return array
     */
    public function getVariables()
    {
        return $this->variable;
    }

    /**
     * @param $variable
     * @return string
     */
    public function replaceVariable($variable)
    {
        if (false === strpos($variable, '%')) {
            return $variable;
        }
        
        $variable = preg_replace_callback(sprintf('/%s(\w*\.*\w*)%s/', $this->delimiter, $this->delimiter), function ($match) use (&$variable) {

            if (!$this->hasVariable($match[1])) {
                throw new \InvalidArgumentException(sprintf('Variable "%s" is undefined.', $match[1]));
            }

            return $this->variable[$match[1]];

        }, $variable);

        return $variable;
    }
}