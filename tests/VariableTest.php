<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */


use FastD\Config\Variable;


class VariableTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Variable
     */
    protected $var;

    public function setUp()
    {
        $this->var = new Variable();
    }

    public function testSetVar()
    {
        $this->var->set('foo', 'bar');
        $this->assertEquals('bar', $this->var->find('foo'));
    }

    public function testReplace()
    {
        $this->var->set('foo', 'bar');
        $this->assertEquals('bar', $this->var->replace('%foo%'));
    }
}
