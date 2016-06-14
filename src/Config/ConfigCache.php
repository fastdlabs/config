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

class ConfigCache
{
    protected $config;

    protected $cache;

    public function __construct(Config $config, $cache)
    {
        $this->config = $config;

        $this->cache = $cache;
    }

    public function getCache()
    {
        return $this->cache;
    }
}