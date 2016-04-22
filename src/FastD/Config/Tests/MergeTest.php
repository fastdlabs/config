<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/8/2
 * Time: 上午12:35
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
use FastD\Config\Loader\YmlFileLoader;

class MergeTest extends \PHPUnit_Framework_TestCase
{
    public function testMerge()
    {
        $config = new Config();
        $config->addLoader(new PhpFileLoader(__DIR__ . '/config/config.php'));
        $config->addLoader(new YmlFileLoader(__DIR__ . '/config/config.yml'));
        print_r($config->all());
    }
}