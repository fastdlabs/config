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
        $this->config = new Config();
    }

    public function testConfig()
    {
        $this->config->load(__DIR__ . '/config/config.php');
        
        $this->assertEquals($this->config->all(), [
            'gender' => 'male',
            'demo' => [
                'name' => 'rt',
                'age' => 16
            ],
            'test' => [
                'test1' => [
                    'name' => 'rt'
                ]
            ],
            "name" => null,
            "age" => 0,
        ]);

        $this->config->load(__DIR__ . '/config/config.ini');

        $this->assertEquals($this->config->all(), [
            'gender' => 'male',
            'demo' => [
                'name' => 'rt',
                'age' => 16
            ],
            'test' => [
                'test1' => [
                    'name' => 'rt'
                ]
            ],
            "name" => null,
            "age" => 22,
        ]);
    }

    public function testCache()
    {
        $config = new Config(__DIR__ );

        $config->load(__DIR__ . '/config/config.ini');

        unset($config);
    }
}
