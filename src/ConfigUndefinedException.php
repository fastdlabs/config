<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Config;

/**
 * Class ConfigUndefinedException
 * @package FastD\Config
 */
class ConfigUndefinedException extends ConfigException
{
    /**
     * ConfigUndefinedException constructor.
     * @param string $key
     */
    public function __construct($key)
    {
        parent::__construct(sprintf('Config "%s" is undefined.', $key));
    }
}
