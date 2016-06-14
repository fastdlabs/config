<?php
/**
 *
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */


namespace FastD\Config\Tests;

use FastD\Config\Config;
use FastD\Config\ConfigCache;

class ConfigCacheTest extends \PHPUnit_Framework_TestCase
{
    public function testCache()
    {
        $config = new Config();

        $config->set('name', 'janhuang');
        $config->set('age', 18);

        $cache = new ConfigCache($config, __DIR__);

        $cache->saveCacheFile();
        $cache->saveCacheFile(ConfigCache::SAVE_CACHE_YML);

        $this->assertTrue(file_exists($cache->getCacheFile()));

        echo $cache->dump();
        echo $cache->dump(ConfigCache::SAVE_CACHE_YML);
    }
}
