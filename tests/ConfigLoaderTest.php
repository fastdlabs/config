<?php

use FastD\Config\ConfigLoader;

/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */
class ConfigLoaderTest extends PHPUnit_Framework_TestCase
{
    public function testLoadYml()
    {
        $config = ConfigLoader::loadYml(__DIR__ . '/config/config.yml');

        $this->assertEquals([
            'name' => 'janhuang',
            'qq' => '%qq%',
            'demo' => [
                'name' => 'janhuang',
                'age' => 18
            ],
            'test' => [
                'test2' => [
                    'name' => 'janhuang'
                ]
            ]
        ], $config);
    }

    public function testLoadIni()
    {
        $config = ConfigLoader::loadIni(__DIR__ . '/config/config.ini');

        $this->assertEquals(['age' => 22], $config);
    }

    public function testLoadPHP()
    {
        $config = ConfigLoader::loadPhp(__DIR__ . '/config/config.php');

        $this->assertEquals($config, [
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
    }

    public function testLoadEnv()
    {
        $config = ConfigLoader::loadEnv(['LANG']);

        $this->assertEquals(['LANG' => 'zh_CN.UTF-8'], $config);
    }

    public function testLoad()
    {
        $config = ConfigLoader::load(__DIR__ . '/config/config.ini');

        $this->assertEquals(['age' => 22], $config);

        $config = ConfigLoader::load(__DIR__ . '/config/config.php');

        $this->assertEquals($config, [
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
    }
}

