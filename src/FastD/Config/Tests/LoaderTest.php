<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/4/3
 * Time: 上午9:59
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */

namespace FastD\Config\Tests;

use FastD\Config\Config;

class LoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    private $config;

    public function setUp()
    {
        $this->config = new Config();

        $this->config->setVariable('qq', '384099566');
        $this->config->setVariable('path', __DIR__);
    }

    public function testLoader()
    {
        $this->config->load(__DIR__ . '/config.yml');

        $this->assertEquals(array(
            'name' => 'janhuang',
            'qq' => '%qq%',
        ), $this->config->all());

        $this->config->load(__DIR__ . '/config.ini');

        $this->assertEquals(array(
            'name' => 'janhuang',
            'qq' => '%qq%',
            'age' => 22
        ), $this->config->all());

        $this->config->load(__DIR__ . '/config.php');

        $this->assertEquals(array(
            'name' => 'janhuang',
            'age' => 22,
            'qq' => '%qq%',
            'gender' => 'male'
        ), $this->config->all());

        $this->assertEquals('janhuang', $this->config->get('name'));

        $this->assertEquals('384099566', $this->config->get('qq'));
    }

    public function testArray()
    {
        $this->config->load(__DIR__ . '/array.yml');

        $this->assertEquals(array(
            123,
            'abc',
            'efg'
        ), $this->config->get('array'));

        $this->assertEquals('abc', $this->config->get('array.1'));
        $this->assertEquals('123', $this->config->get('array.0'));
        $this->assertEquals('janhuang', $this->config->get('array2.name'));
    }
}