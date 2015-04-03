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

namespace Dobee\Config\Tests;

use Dobee\Config\Config;

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
        ), $this->config->getParameters());

        $this->config->load(__DIR__ . '/config.ini');

        $this->assertEquals(array(
            'name' => 'janhuang',
            'qq' => '%qq%',
            'age' => 22
        ), $this->config->getParameters());

        $this->config->load(__DIR__ . '/config.php');

        $this->assertEquals(array(
            'name' => 'janhuang',
            'age' => 22,
            'qq' => '%qq%',
            'gender' => 'male'
        ), $this->config->getParameters());

        $this->assertEquals('janhuang', $this->config->getParameters('name'));

        $this->assertEquals('384099566', $this->config->getParameters('qq'));
    }
}