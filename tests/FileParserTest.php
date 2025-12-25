<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */
use FastD\Config\FileParser;
use FastD\Config\Parsed;

class FileParserTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FileParser
     */
    protected FileParser $parser;

    protected function setUp(): void
    {
        $this->parser = new FileParser();
    }

    public function testLoad()
    {
        $parsed = $this->parser->parse(__DIR__.'/config/config.yml');
        $this->assertEquals('yml', $parsed->get('foo'));
    }

    public function testVar()
    {
        // 测试变量替换功能
        $vars = [
            'name' => 'FastD',
            'user.school' => 'Example University'
        ];

        $parser = new FileParser($vars);
        $parsed = $parser->parse(__DIR__.'/config/variable.yml');

        $this->assertEquals('FastD', $parsed->get('name'));
        $this->assertEquals('Example University', $parsed->get('school'));
    }

    public function testVariableReplacementInNestedArray()
    {
        // 创建一个包含变量的临时JSON配置文件
        $configContent = [
            'database' => [
                'host' => '%db_host%',
                'port' => '%db_port%',
                'name' => 'test_db'
            ],
            'cache' => [
                'host' => '%cache_host%',
                'type' => 'redis'
            ],
            'debug' => '%app_debug%'
        ];
        
        $tempFile = tempnam(sys_get_temp_dir(), 'config_test') . '.json';
        file_put_contents($tempFile, json_encode($configContent));
        
        $vars = [
            'db_host' => 'localhost',
            'db_port' => 3306,
            'cache_host' => '127.0.0.1',
            'app_debug' => true
        ];
        
        $parser = new FileParser($vars);
        $parsed = $parser->parse($tempFile);
        
        $this->assertEquals('localhost', $parsed->get('database.host'));
        $this->assertEquals(3306, $parsed->get('database.port'));
        $this->assertEquals('127.0.0.1', $parsed->get('cache.host'));
        $this->assertTrue((bool)$parsed->get('debug'));

        unlink($tempFile);
    }

    public function testVariableReplacementWithStringValues()
    {
        $configContent = [
            'app_name' => '%name%',
            'version' => '%version%',
            'description' => 'This is %name% application with version %version%'
        ];

        $tempFile = tempnam(sys_get_temp_dir(), 'config_test') . '.json';
        file_put_contents($tempFile, json_encode($configContent));

        $vars = [
            'name' => 'MyApp',
            'version' => '1.0.0'
        ];

        $parser = new FileParser($vars);
        $parsed = $parser->parse($tempFile);

        $this->assertEquals('MyApp', $parsed->get('app_name'));
        $this->assertEquals('1.0.0', $parsed->get('version'));
        $this->assertEquals('This is MyApp application with version 1.0.0', $parsed->get('description'));

        unlink($tempFile);
    }

    public function testVariableReplacementWithNonExistentVariables()
    {
        $configContent = [
            'existing_var' => '%name%',
            'non_existing_var' => '%non_existing%',
            'mixed_content' => 'Value is %name% but %non_existing% is missing'
        ];

        $tempFile = tempnam(sys_get_temp_dir(), 'config_test') . '.json';
        file_put_contents($tempFile, json_encode($configContent));

        $vars = [
            'name' => 'Found'
        ];

        $parser = new FileParser($vars);
        $parsed = $parser->parse($tempFile);

        $this->assertEquals('Found', $parsed->get('existing_var'));
        $this->assertEquals('%non_existing%', $parsed->get('non_existing_var'));
        $this->assertEquals('Value is Found but %non_existing% is missing', $parsed->get('mixed_content'));

        unlink($tempFile);
    }

    public function testIniFileVariableReplacement()
    {
        $configContent = "app_name=%name%\ndb_host=%db_host%";
        $tempFile = tempnam(sys_get_temp_dir(), 'config_test') . '.ini';
        file_put_contents($tempFile, $configContent);

        $vars = [
            'name' => 'TestApp',
            'db_host' => 'localhost'
        ];

        $parser = new FileParser($vars);
        $parsed = $parser->parse($tempFile);

        $this->assertEquals('TestApp', $parsed->get('app_name'));
        $this->assertEquals('localhost', $parsed->get('db_host'));

        unlink($tempFile);
    }

    public function testYamlFileVariableReplacement()
    {
        $configContent = "app_name: \"%name%\"\ndb:\n  host: \"%db_host%\"\n  port: \"%db_port%\"";
        $tempFile = tempnam(sys_get_temp_dir(), 'config_test') . '.yml';
        file_put_contents($tempFile, $configContent);

        $vars = [
            'name' => 'YamlApp',
            'db_host' => 'yaml_host',
            'db_port' => 5432
        ];

        $parser = new FileParser($vars);
        $parsed = $parser->parse($tempFile);

        $this->assertEquals('YamlApp', $parsed->get('app_name'));
        $this->assertEquals('yaml_host', $parsed->get('db.host'));
        $this->assertEquals(5432, $parsed->get('db.port'));

        unlink($tempFile);
    }
}