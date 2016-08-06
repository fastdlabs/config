<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/4/23
 * Time: 下午1:06
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

use FastD\Config\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    protected $config;

    public function setUp()
    {
        $this->config = new Config();
    }

    public function testConfig()
    {
        $this->config->load(__DIR__ . '/config/config.ini');

        $this->assertEquals(['age' => 22], $this->config->all());

        $this->config->load(__DIR__ . '/config/array.yml');

        $this->assertEquals($this->config->all(), [
            'age' => 22,
            'array' => [
                123,
                'abc',
                'efg'
            ],
            'array2' => [
                'name' => 'janhuang',
                'age' => 22
            ]
        ]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConfigGet()
    {
        $this->config->load(__DIR__ . '/config/array.yml');

        $this->assertEquals('abc', $this->config->get('array.1'));

        $this->assertEquals('janhuang', $this->config->get('array2.name'));

        $this->config->get('undefined');
    }

    public function testConfigHas()
    {
        $this->config->load(__DIR__ . '/config/array.yml');

        $this->assertTrue($this->config->has('array2.name'));

        $this->assertFalse($this->config->has('array.name'));
    }

    public function testVariable()
    {
        $this->config->setVariable('name', 'jan');

        $this->config->load(__DIR__ . '/config/variable.yml');

        $this->assertEquals('jan', $this->config->get('name'));
    }

    public function testAutoCache()
    {
        $config = new Config(__DIR__ . '/auto', null, true);

        $cloneConfig = clone $config;

        unset($config);

        $this->assertTrue(file_exists($cloneConfig->getCache()->getCacheFile()));
    }
}
