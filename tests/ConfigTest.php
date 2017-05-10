<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
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
        $this->config = new Config([], [
            'name' => 'bar'
        ]);
    }

    public function testLoad()
    {
        $this->config->load(__DIR__ . '/config/config.yml');

        $this->assertEquals('yml', $this->config->find('foo'));
        $this->assertEquals([
            'foo' => 'yml'
        ], $this->config->all());
    }

    public function testVariable()
    {
        $this->config->load(__DIR__ . '/config/variable.yml');
        $this->assertEquals('bar', $this->config->get('name'));
    }

    public function testSetConfig()
    {
        $this->config->set('a', 'hello');
        $this->config->set('a.b.c', 'world');
        $this->assertEquals([
            'a' => [
                'hello',
                'b' => [
                    'c' => 'world'
                ],
            ],
        ], $this->config->all());
        $this->config->set('a.b', 'world');
        $this->assertEquals([
            'a' => [
                'hello',
                'b' => 'world',
            ],
        ], $this->config->all());
    }

    public function testHasConfig()
    {
        $this->config->set('a', [
            'b' => [
                'c' => 'hello world',
            ],
        ]);
        $this->assertSame(true, $this->config->has('a'));
        $this->assertSame(true, $this->config->has('a.b'));
        $this->assertSame(true, $this->config->has('a.b.c'));
        $this->assertSame(false, $this->config->has('a.b.cd'));
        $this->assertSame(false, $this->config->has('b'));
        $this->assertSame(false, $this->config->has('b.c'));
    }
}
