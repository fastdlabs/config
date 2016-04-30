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

namespace FastD\Config;

/**
 * Class Variable
 *
 * @package FastD\Configuration
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
    public function set($name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                $this->variable[$key] = $value;
            }

            return $this;
        }

        $this->variable[$name] = $value;

        return $this;
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->variable[$name]);
    }

    /**
     * @param $name
     * @return string
     */
    public function get($name)
    {
        if (!$this->has($name)) {
            throw new \InvalidArgumentException(sprintf('Variable "%s" is undefined.', $name));
        }

        return $this->variable[$name];
    }

    /**
     * @param $variable
     * @return string
     */
    public function replace($variable)
    {
        if (empty($variable) || false === strpos($variable, '%')) {
            return $variable;
        }

        $variable = preg_replace_callback(sprintf('/%s(\w*\.*\w*)%s/', $this->delimiter, $this->delimiter), function ($match) use (&$variable) {

            if (!$this->has($match[1])) {
                throw new \InvalidArgumentException(sprintf('Variable "%s" is undefined.', $match[1]));
            }

            return $this->variable[$match[1]];

        }, $variable);

        return $variable;
    }
}