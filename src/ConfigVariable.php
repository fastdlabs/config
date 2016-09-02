<?php
/**
 *
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Config;

/**
 * Class ConfigVariable
 *
 * @package FastD\Config
 */
class ConfigVariable
{
    /**
     * @var array
     */
    private $variable = array();

    const DELIMITER = '%';

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
            throw new ConfigVariableUndefinedException($name);
        }

        return $this->variable[$name];
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->variable;
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

        $variable = preg_replace_callback(sprintf('/%s(\w*\.*\w*)%s/', static::DELIMITER, static::DELIMITER), function ($match) use (&$variable) {

            if (!$this->has($match[1])) {
                throw new ConfigVariableUndefinedException($match[1]);
            }

            return $this->variable[$match[1]];

        }, $variable);

        return $variable;
    }
}