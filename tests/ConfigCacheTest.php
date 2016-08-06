<?php
/**
 *
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

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

        $this->assertEquals('.user.php.cache', $cache->getCacheFileName());
        $this->assertEquals('.user.ini.cache', $cache->getCacheFileName(ConfigCache::CACHE_INI));
        $this->assertEquals('.user.yml.cache', $cache->getCacheFileName(ConfigCache::CACHE_YML));

        $cache->saveCacheFile();
        $cache->saveCacheFile(ConfigCache::CACHE_YML);

        $this->assertTrue(file_exists($cache->getCacheFile()));
        $this->assertTrue(file_exists($cache->getCacheFile(ConfigCache::CACHE_YML)));
    }

    public function testLoadCache()
    {
        $config = new Config();

        $cache = new ConfigCache($config, __DIR__);
    }
}
