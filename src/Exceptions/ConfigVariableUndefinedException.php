<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Config\Exceptions;

/**
 * Class ConfigVariableUndefinedException
 * @package FastD\Config
 */
class ConfigVariableUndefinedException extends ConfigException
{
    /**
     * ConfigVariableUndefinedException constructor.
     * @param string $key
     */
    public function __construct($key)
    {
        parent::__construct(sprintf('Config variable "%s" is undefined.', $key));
    }
}
