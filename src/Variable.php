<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Config;

use FastD\Config\Exceptions\ConfigVariableUndefinedException;
use FastD\Utils\Arr;

/**
 * Class ConfigVariable
 *
 * @package FastD\Config
 */
class Variable extends Arr
{
    const DELIMITER = '%';

    /**
     * Variable constructor.
     * @param array $variable
     */
    public function __construct(array $variable = [])
    {
        parent::__construct($variable);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->toRaw();
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
            return $this->data[$match[1]];
        }, $variable);

        return $variable;
    }
}