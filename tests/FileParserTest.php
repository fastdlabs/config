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
        $this->assertEquals('yml', $parsed->get('config.foo'));
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

        $this->assertEquals('FastD', $parsed->get('variable.name'));
        $this->assertEquals('Example University', $parsed->get('variable.school'));
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
        $filename = pathinfo($tempFile, PATHINFO_FILENAME);
        file_put_contents($tempFile, json_encode($configContent));
        
        $vars = [
            'db_host' => 'localhost',
            'db_port' => 3306,
            'cache_host' => '127.0.0.1',
            'app_debug' => true
        ];
        
        $parser = new FileParser($vars);
        $parsed = $parser->parse($tempFile);
        
        $this->assertEquals('localhost', $parsed->get("{$filename}.database.host"));
        $this->assertEquals(3306, $parsed->get("{$filename}.database.port"));
        $this->assertEquals('127.0.0.1', $parsed->get("{$filename}.cache.host"));
        $this->assertTrue((bool)$parsed->get("{$filename}.debug"));

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
        $filename = pathinfo($tempFile, PATHINFO_FILENAME);

        $vars = [
            'name' => 'MyApp',
            'version' => '1.0.0'
        ];

        $parser = new FileParser($vars);
        $parsed = $parser->parse($tempFile);

        $this->assertEquals('MyApp', $parsed->get($filename . '.app_name'));
        $this->assertEquals('1.0.0', $parsed->get($filename . '.version'));
        $this->assertEquals('This is MyApp application with version 1.0.0', $parsed->get($filename . '.description'));

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
        $filename = pathinfo($tempFile, PATHINFO_FILENAME);

        $vars = [
            'name' => 'Found'
        ];

        $parser = new FileParser($vars);
        $parsed = $parser->parse($tempFile);

        $this->assertEquals('Found', $parsed->get($filename . '.existing_var'));
        $this->assertEquals('%non_existing%', $parsed->get($filename . '.non_existing_var'));
        $this->assertEquals('Value is Found but %non_existing% is missing', $parsed->get($filename . '.mixed_content'));

        unlink($tempFile);
    }

    public function testIniFileVariableReplacement()
    {
        $configContent = "app_name=%name%\ndb_host=%db_host%";
        $tempFile = tempnam(sys_get_temp_dir(), 'config_test') . '.ini';
        file_put_contents($tempFile, $configContent);
        $filename = pathinfo($tempFile, PATHINFO_FILENAME);
        $vars = [
            'name' => 'TestApp',
            'db_host' => 'localhost'
        ];

        $parser = new FileParser($vars);
        $parsed = $parser->parse($tempFile);

        $this->assertEquals('TestApp', $parsed->get($filename . '.app_name'));
        $this->assertEquals('localhost', $parsed->get($filename . '.db_host'));

        unlink($tempFile);
    }

    public function testYamlFileVariableReplacement()
    {
        $configContent = "app_name: \"%name%\"\ndb:\n  host: \"%db_host%\"\n  port: \"%db_port%\"";
        $tempFile = tempnam(sys_get_temp_dir(), 'config_test') . '.yml';
        file_put_contents($tempFile, $configContent);
        $filename = pathinfo($tempFile, PATHINFO_FILENAME);
        $vars = [
            'name' => 'YamlApp',
            'db_host' => 'yaml_host',
            'db_port' => 5432
        ];

        $parser = new FileParser($vars);
        $parsed = $parser->parse($tempFile);

        $this->assertEquals('YamlApp', $parsed->get($filename . '.app_name'));
        $this->assertEquals('yaml_host', $parsed->get($filename . '.db.host'));
        $this->assertEquals(5432, $parsed->get($filename . '.db.port'));
        $this->assertEquals(5432, $parsed[$filename . '.db.port']);

        unlink($tempFile);
    }

    public function testSetMethod()
    {
        $parsed = new Parsed();
        
        // 测试设置简单键值对
        $parsed->set('app_name', 'TestApp');
        $this->assertEquals('TestApp', $parsed->get('app_name'));
        
        // 测试设置嵌套键
        $parsed->set('database.host', 'localhost');
        $this->assertEquals('localhost', $parsed->get('database.host'));
        
        // 测试设置多层嵌套键
        $parsed->set('database.connection.options.timeout', 30);
        $this->assertEquals(30, $parsed->get('database.connection.options.timeout'));
        
        // 测试覆盖已存在的值
        $parsed->set('app_name', 'NewApp');
        $this->assertEquals('NewApp', $parsed->get('app_name'));
    }

    public function testHasMethod()
    {
        $parsed = new Parsed([
            'app' => [
                'name' => 'TestApp',
                'version' => '1.0'
            ],
            'database' => [
                'host' => 'localhost',
                'port' => 3306
            ],
            'debug' => true
        ]);
        
        // 测试存在的一级键
        $this->assertTrue($parsed->has('app'));
        $this->assertTrue($parsed->has('debug'));
        
        // 测试存在的嵌套键
        $this->assertTrue($parsed->has('app.name'));
        $this->assertTrue($parsed->has('app.version'));
        $this->assertTrue($parsed->has('database.host'));
        $this->assertTrue($parsed->has('database.port'));
        
        // 测试不存在的键
        $this->assertFalse($parsed->has('nonexistent'));
        $this->assertFalse($parsed->has('app.author'));
        $this->assertFalse($parsed->has('database.user'));
        
        // 测试部分路径存在的键
        $this->assertFalse($parsed->has('app.name.version')); // app.name是字符串，不是数组，所以不存在version子键
    }

    public function testOverallStructureValidation()
    {
        $configContent = [
            'app' => [
                'name' => 'MyApp',
                'version' => '1.0.0',
                'env' => '%env%',
                'debug' => true
            ],
            'database' => [
                'driver' => 'mysql',
                'host' => '%db_host%',
                'port' => '%db_port%',
                'database' => '%db_name%',
                'username' => '%db_user%',
                'password' => '%db_pass%',
                'options' => [
                    'charset' => 'utf8',
                    'timeout' => 30
                ]
            ],
            'cache' => [
                'type' => 'redis',
                'host' => '%cache_host%',
                'port' => '%cache_port%'
            ],
            'services' => [
                'logger' => [
                    'level' => 'debug',
                    'path' => '/var/log/app.log'
                ],
                'mailer' => [
                    'host' => '%smtp_host%',
                    'port' => '%smtp_port%',
                    'username' => '%smtp_user%',
                    'password' => '%smtp_pass%'
                ]
            ]
        ];

        $tempFile = tempnam(sys_get_temp_dir(), 'config_test') . '.json';
        file_put_contents($tempFile, json_encode($configContent));
        $filename = pathinfo($tempFile, PATHINFO_FILENAME);

        $vars = [
            'env' => 'production',
            'db_host' => 'localhost',
            'db_port' => 3306,
            'db_name' => 'myapp',
            'db_user' => 'root',
            'db_pass' => 'password',
            'cache_host' => '127.0.0.1',
            'cache_port' => 6379,
            'smtp_host' => 'smtp.gmail.com',
            'smtp_port' => 587,
            'smtp_user' => 'user@gmail.com',
            'smtp_pass' => 'password'
        ];

        $parser = new FileParser($vars);
        $parsed = $parser->parse($tempFile);

        // 验证整体结构
        $this->assertTrue($parsed->has($filename . '.app'));
        $this->assertTrue($parsed->has($filename . '.database'));
        $this->assertTrue($parsed->has($filename . '.cache'));
        $this->assertTrue($parsed->has($filename . '.services'));

        // 验证应用配置
        $this->assertEquals('MyApp', $parsed->get($filename . '.app.name'));
        $this->assertEquals('1.0.0', $parsed->get($filename . '.app.version'));
        $this->assertEquals('production', $parsed->get($filename . '.app.env'));
        $this->assertTrue($parsed->get($filename . '.app.debug'));

        // 验证数据库配置
        $this->assertEquals('mysql', $parsed->get($filename . '.database.driver'));
        $this->assertEquals('localhost', $parsed->get($filename . '.database.host'));
        $this->assertEquals(3306, $parsed->get($filename . '.database.port'));
        $this->assertEquals('myapp', $parsed->get($filename . '.database.database'));
        $this->assertEquals('root', $parsed->get($filename . '.database.username'));
        $this->assertEquals('password', $parsed->get($filename . '.database.password'));
        $this->assertEquals('utf8', $parsed->get($filename . '.database.options.charset'));
        $this->assertEquals(30, $parsed->get($filename . '.database.options.timeout'));

        // 验证缓存配置
        $this->assertEquals('redis', $parsed->get($filename . '.cache.type'));
        $this->assertEquals('127.0.0.1', $parsed->get($filename . '.cache.host'));
        $this->assertEquals(6379, $parsed->get($filename . '.cache.port'));

        // 验证服务配置
        $this->assertEquals('debug', $parsed->get($filename . '.services.logger.level'));
        $this->assertEquals('/var/log/app.log', $parsed->get($filename . '.services.logger.path'));
        $this->assertEquals('smtp.gmail.com', $parsed->get($filename . '.services.mailer.host'));
        $this->assertEquals(587, $parsed->get($filename . '.services.mailer.port'));
        $this->assertEquals('user@gmail.com', $parsed->get($filename . '.services.mailer.username'));
        $this->assertEquals('password', $parsed->get($filename . '.services.mailer.password'));

        unlink($tempFile);
    }

    public function testArrayAccessGet()
    {
        $parsed = new Parsed([
            'app' => [
                'name' => 'TestApp',
                'version' => '1.0'
            ],
            'database' => [
                'host' => 'localhost',
                'port' => 3306
            ],
            'debug' => true
        ]);

        // 测试通过数组方式获取简单值
        $this->assertEquals('TestApp', $parsed['app.name']);
        $this->assertEquals('1.0', $parsed['app.version']);
        $this->assertEquals('localhost', $parsed['database.host']);
        $this->assertEquals(3306, $parsed['database.port']);
        $this->assertTrue($parsed['debug']);

        // 测试通过数组方式获取嵌套数组
        $this->assertEquals(['name' => 'TestApp', 'version' => '1.0'], $parsed['app']);
        $this->assertEquals(['host' => 'localhost', 'port' => 3306], $parsed['database']);

        // 测试不存在的键返回null
        $this->assertNull($parsed['nonexistent']);
        $this->assertNull($parsed['app.nonexistent']);
    }

    public function testArrayAccessSet()
    {
        $parsed = new Parsed();

        // 测试通过数组方式设置简单键值对
        $parsed['app_name'] = 'TestApp';
        $this->assertEquals('TestApp', $parsed->get('app_name'));
        $this->assertEquals('TestApp', $parsed['app_name']);

        // 测试通过数组方式设置嵌套键
        $parsed['database.host'] = 'localhost';
        $this->assertEquals('localhost', $parsed->get('database.host'));
        $this->assertEquals('localhost', $parsed['database.host']);

        // 测试通过数组方式设置多层嵌套键
        $parsed['database.connection.options.timeout'] = 30;
        $this->assertEquals(30, $parsed->get('database.connection.options.timeout'));
        $this->assertEquals(30, $parsed['database.connection.options.timeout']);

        // 测试通过数组方式覆盖已存在的值
        $parsed['app_name'] = 'NewApp';
        $this->assertEquals('NewApp', $parsed->get('app_name'));
        $this->assertEquals('NewApp', $parsed['app_name']);

        // 测试数组方式与set方法的一致性
        $parsed->set('api.key', 'test_key');
        $this->assertEquals('test_key', $parsed['api.key']);
        
        $parsed['api.secret'] = 'test_secret';
        $this->assertEquals('test_secret', $parsed->get('api.secret'));
    }

    public function testArrayAccessExists()
    {
        $parsed = new Parsed([
            'app' => [
                'name' => 'TestApp',
                'version' => '1.0'
            ],
            'database' => [
                'host' => 'localhost',
                'port' => 3306
            ],
            'debug' => true
        ]);

        // 测试通过数组方式检查键是否存在
        $this->assertTrue(isset($parsed['app']));
        $this->assertTrue(isset($parsed['app.name']));
        $this->assertTrue(isset($parsed['app.version']));
        $this->assertTrue(isset($parsed['database.host']));
        $this->assertTrue(isset($parsed['database.port']));
        $this->assertTrue(isset($parsed['debug']));

        // 测试不存在的键
        $this->assertFalse(isset($parsed['nonexistent']));
        $this->assertFalse(isset($parsed['app.author']));
        $this->assertFalse(isset($parsed['database.user']));

        // 测试通过数组方式设置后检查存在性
        $parsed['new_key'] = 'new_value';
        $this->assertTrue(isset($parsed['new_key']));
    }

    public function testArrayAccessUnset()
    {
        $parsed = new Parsed([
            'app' => [
                'name' => 'TestApp',
                'version' => '1.0'
            ],
            'database' => [
                'host' => 'localhost',
                'port' => 3306
            ],
            'debug' => true
        ]);

        // 测试通过数组方式删除键
        unset($parsed['app.name']);
        $this->assertFalse(isset($parsed['app.name']));
        $this->assertTrue(isset($parsed['app'])); // app 仍然存在，只是 name 被删除了
        $this->assertNull($parsed['app.name']);

        // 测试删除整个嵌套结构
        unset($parsed['database']);
        $this->assertFalse(isset($parsed['database']));
        $this->assertFalse(isset($parsed['database.host']));
        $this->assertNull($parsed['database']);
    }
}