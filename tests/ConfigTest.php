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

class ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    protected $config;

    public function setUp()
    {
        $this->config = new Config([
            'name' => '%name%'
        ]);
    }

    public function testLoad()
    {
        $this->config->load(__DIR__ . '/config/array.yml');

        $this->assertEquals('yml', $this->config->find('foo'));
        $this->assertEquals([
            'name' => '%name%',
            'foo' => 'yml'
        ], $this->config->all());
    }

    public function testVariable()
    {
        $this->config->setVariable('name', 'bar');
        $this->assertEquals('bar', $this->config->find('name'));
    }
}
