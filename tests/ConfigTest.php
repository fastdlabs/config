<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */
use FastD\Config\Config;

class ConfigTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Config
     */
    protected $config;

    protected function setUp(): void
    {
        $this->config = new Config([], [
            'name' => 'bar',
        ]);
    }

    public function testLoad()
    {
        $this->config->load(__DIR__.'/config/config.yml');

        $config = $this->config->all();

        $this->assertEquals($config, [
            'foo' => 'yml',
            'profile' => [
                'nickname' => 'janhuang',
                'age' => 18,
            ]
        ]);
    }

    public function testMerge()
    {
        $this->config->load(__DIR__.'/config/config.yml');

        $this->config->load(__DIR__ . '/config/config.ini');

        $config = $this->config->all();

        $this->assertEquals($config, [
            'foo' => 'bar',
            'profile' => [
                'nickname' => 'janhuang',
                'age' => 18,
            ],
        ]);
    }

    public function testGet()
    {
        $this->config->load(__DIR__.'/config/config.yml');

        $nickname = $this->config->get('profile.nickname');

        $this->assertEquals('janhuang', $nickname);
    }

    public function testHas()
    {
        $this->config->load(__DIR__.'/config/config.yml');

        $exists = $this->config->has('profile.nickname');
        $notExists = $this->config->has('profile.gender');

        $this->assertTrue($exists);
        $this->assertNotTrue($notExists);
    }

    public function testSet()
    {
        $this->config->load(__DIR__.'/config/config.yml');

        $this->config->set('marry', true);
        $this->config->set('profile.marry', true);
        $this->assertTrue($this->config->get('marry'));
        $this->assertTrue($this->config->get('profile.marry'));
    }

    public function testVar()
    {
        $this->config->load(__DIR__.'/config/variable.yml');
        $name = $this->config->get('name');

        $this->assertEquals('bar', $name);
    }
}
