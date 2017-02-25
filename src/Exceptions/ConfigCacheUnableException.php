<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Config\Exceptions;

class ConfigCacheUnableException extends ConfigException
{
    public function __construct($cache)
    {
        parent::__construct(sprintf('Config caching file "%s" cannot open.', $cache));
    }
}