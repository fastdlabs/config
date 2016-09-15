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
        $cache = new ConfigCache(__DIR__ . '/cache');

        $data = [
            'name' => 'jan',
        ];

        $dump = $cache->dump($data);

        $this->assertEquals('<?php return array (
  \'name\' => \'jan\',
);', $dump);

        $cache->saveCache($data);

        $cacheData = $cache->loadCache();

        $this->assertEquals($data, $cacheData);
    }

    /**
     * @expectedException FastD\Config\Exceptions\ConfigCacheUnableException
     */
    public function testCannotConfigCacheException()
    {
        $cache = new ConfigCache();

        $cache->loadCache();
    }

    /**
     * @expectedException FastD\Config\Exceptions\ConfigCacheUnableException
     */
    public function testNullConfigCacheException()
    {
        $cache = new ConfigCache();

        $cache->saveCache([]);
    }
}
