<?php

/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */
class HelperTest extends PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $this->assertEquals(load(__DIR__.'/config/config.ini'), ['foo' => 'bar']);
        $this->assertEquals(load(__DIR__.'/config/config.yml'), ['foo' => 'yml']);
    }
}
