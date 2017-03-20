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
}
