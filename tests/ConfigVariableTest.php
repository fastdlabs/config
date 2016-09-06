<?php
use FastD\Config\ConfigVariable;

/**
 *
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */
class ConfigVariableTest extends PHPUnit_Framework_TestCase
{
    public function testConfigVariables()
    {
        $variable = new ConfigVariable();

        $variable->set('name', 'jan');

        $this->assertEquals('jan', $variable->get('name'));
    }

    public function testConfigVariableReplace()
    {
        $variable = new ConfigVariable();

        $variable->set('name', 'jan');

        $this->assertEquals('jan', $variable->replace('%name%'));
    }
}
