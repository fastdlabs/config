<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/9/7
 * Time: 下午4:11
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Config\Tests;

use FastD\Config\Config;
use FastD\Config\Loader\PhpFileLoader;

class EmptyTest extends \PHPUnit_Framework_TestCase
{
    public function testGetParameters()
    {
        $config = new Config();
        $config->addLoader(new PhpFileLoader(__DIR__ . '/config.php'));
        $this->assertEquals(null, $config->get('name'));
        $this->assertEquals(0, $config->get('age'));
        $this->assertEquals('', $config->get('gender'));
    }
}