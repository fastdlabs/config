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
use Symfony\Component\Yaml\Yaml;

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
        $configContent = [
            'config' => [
                'foo' => 'yml'
            ]
        ];

        $tempFile = tempnam(sys_get_temp_dir(), 'config_test') . '.yml';
        file_put_contents($tempFile, Yaml::dump($configContent));
        $filename = pathinfo($tempFile, PATHINFO_FILENAME);

        $parsed = $this->parser->parse($tempFile);
        $this->assertEquals('yml', $parsed->get($filename . '.config.foo'));

        unlink($tempFile);
    }

    public function testVar()
    {
        // 测试变量替换功能
        $vars = [
            'name' => 'FastD',
            'user.school' => 'Example University'
        ];

        $configContent = [
            'variable' => [
                'name' => '%name%',
                'school' => '%user.school%'
            ]
        ];

        $tempFile = tempnam(sys_get_temp_dir(), 'config_test') . '.yml';
        file_put_contents($tempFile, Yaml::dump($configContent));
        $filename = pathinfo($tempFile, PATHINFO_FILENAME);

        $parser = new FileParser($vars);
        $parsed = $parser->parse($tempFile);

        $this->assertEquals('FastD', $parsed->get($filename . '.variable.name'));
        $this->assertEquals('Example University', $parsed->get($filename . '.variable.school'));

        unlink($tempFile);
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

    // 新增测试方法

    public function testLoadJsonFile()
    {
        $configContent = [
            'app' => [
                'name' => 'TestApp',
                'version' => '1.0.0'
            ],
            'database' => [
                'host' => 'localhost',
                'port' => 3306
            ]
        ];

        $tempFile = tempnam(sys_get_temp_dir(), 'config_test') . '.json';
        file_put_contents($tempFile, json_encode($configContent));

        $result = $this->parser->load($tempFile);
        
        $this->assertEquals($configContent, $result);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('app', $result);
        $this->assertArrayHasKey('database', $result);
        $this->assertEquals('TestApp', $result['app']['name']);

        unlink($tempFile);
    }

    public function testLoadYamlFile()
    {
        $configContent = "app:\n  name: TestYamlApp\n  version: 2.0.0\ndatabase:\n  host: yaml-host\n  port: 5432";
        
        $tempFile = tempnam(sys_get_temp_dir(), 'config_test') . '.yml';
        file_put_contents($tempFile, $configContent);

        $result = $this->parser->load($tempFile);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('app', $result);
        $this->assertArrayHasKey('database', $result);
        $this->assertEquals('TestYamlApp', $result['app']['name']);
        $this->assertEquals('2.0.0', $result['app']['version']);
        $this->assertEquals('yaml-host', $result['database']['host']);

        unlink($tempFile);
    }

    public function testLoadIniFile()
    {
        $configContent = "[app]\nname = TestIniApp\nversion = 3.0.0\n[database]\nhost = ini-host\nport = 1234";
        
        $tempFile = tempnam(sys_get_temp_dir(), 'config_test') . '.ini';
        file_put_contents($tempFile, $configContent);

        $result = $this->parser->load($tempFile);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('app', $result);
        $this->assertArrayHasKey('database', $result);
        $this->assertEquals('TestIniApp', $result['app']['name']);
        $this->assertEquals('3.0.0', $result['app']['version']);
        $this->assertEquals('ini-host', $result['database']['host']);
        $this->assertEquals('1234', $result['database']['port']); // 注意：parse_ini_file 返回字符串

        unlink($tempFile);
    }

    public function testLoadPhpFile()
    {
        $configContent = "<?php\nreturn [\n    'app' => [\n        'name' => 'TestPhpApp',\n        'version' => '4.0.0'\n    ],\n    'database' => [\n        'host' => 'php-host',\n        'port' => 5678\n    ]\n];";
        
        $tempFile = tempnam(sys_get_temp_dir(), 'config_test') . '.php';
        file_put_contents($tempFile, $configContent);

        $result = $this->parser->load($tempFile);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('app', $result);
        $this->assertArrayHasKey('database', $result);
        $this->assertEquals('TestPhpApp', $result['app']['name']);
        $this->assertEquals('4.0.0', $result['app']['version']);
        $this->assertEquals('php-host', $result['database']['host']);
        $this->assertEquals(5678, $result['database']['port']);

        unlink($tempFile);
    }

    public function testLoadUnsupportedFileType()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'config_test') . '.txt';
        file_put_contents($tempFile, 'unsupported content');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unsupported file type: ' . $tempFile);
        
        $this->parser->load($tempFile);

        unlink($tempFile);
    }

    public function testConstructorWithFilePath()
    {
        $configContent = [
            'variables' => [
                'app_name' => 'FromConstructorApp',
                'version' => '5.0.0'
            ]
        ];

        $tempFile = tempnam(sys_get_temp_dir(), 'config_test') . '.json';
        file_put_contents($tempFile, json_encode($configContent));

        $parser = new FileParser($tempFile);
        
        // 验证变量已加载
        $this->assertEquals('FromConstructorApp', $parser->var->get('variables.app_name'));
        $this->assertEquals('5.0.0', $parser->var->get('variables.version'));

        unlink($tempFile);
    }

    public function testReplaceMethodWithComplexNestedStructure()
    {
        $testData = [
            'level1' => [
                'level2' => [
                    'level3' => [
                        'value' => 'original_%var1%_and_%var2%',
                        'another' => '%var3%'
                    ]
                ],
                'simple' => 'just_%var1%_here'
            ],
            'direct' => '%var2%',
            'no_vars' => 'no variables here'
        ];

        $vars = [
            'var1' => 'REPLACED_VAR1',
            'var2' => 'REPLACED_VAR2',
            'var3' => 'REPLACED_VAR3'
        ];

        $parser = new FileParser($vars);
        
        // 使用反射访问受保护的 replace 方法
        $reflectionClass = new \ReflectionClass(FileParser::class);
        $replaceMethod = $reflectionClass->getMethod('replace');
        $replaceMethod->setAccessible(true);
        
        $result = $replaceMethod->invoke($parser, $testData);
        
        $this->assertEquals('original_REPLACED_VAR1_and_REPLACED_VAR2', $result['level1']['level2']['level3']['value']);
        $this->assertEquals('REPLACED_VAR3', $result['level1']['level2']['level3']['another']);
        $this->assertEquals('just_REPLACED_VAR1_here', $result['level1']['simple']);
        $this->assertEquals('REPLACED_VAR2', $result['direct']);
        $this->assertEquals('no variables here', $result['no_vars']);
    }

    public function testReplaceMethodWithNonStringValues()
    {
        $testData = [
            'string_val' => '%var1%',
            'int_val' => 123,
            'float_val' => 45.67,
            'bool_true' => true,
            'bool_false' => false,
            'null_val' => null,
            'array_val' => ['item1', 'item2']
        ];

        $vars = [
            'var1' => 'replaced_value'
        ];

        $parser = new FileParser($vars);
        
        // 使用反射访问受保护的 replace 方法
        $reflectionClass = new \ReflectionClass(FileParser::class);
        $replaceMethod = $reflectionClass->getMethod('replace');
        $replaceMethod->setAccessible(true);
        
        $result = $replaceMethod->invoke($parser, $testData);
        
        $this->assertEquals('replaced_value', $result['string_val']);
        $this->assertEquals(123, $result['int_val']);
        $this->assertEquals(45.67, $result['float_val']);
        $this->assertTrue($result['bool_true']);
        $this->assertFalse($result['bool_false']);
        $this->assertNull($result['null_val']);
        $this->assertEquals(['item1', 'item2'], $result['array_val']);
    }

    public function testUnsetMethod()
    {
        $parsed = new Parsed([
            'app' => [
                'name' => 'TestApp',
                'version' => '1.0.0',
                'nested' => [
                    'deep' => [
                        'value' => 'deep_value'
                    ]
                ]
            ],
            'database' => [
                'host' => 'localhost',
                'port' => 3306
            ]
        ]);

        // 测试删除一级键
        $this->assertTrue($parsed->has('database'));
        $parsed->unset('database');
        $this->assertFalse($parsed->has('database'));

        // 测试删除嵌套键
        $this->assertTrue($parsed->has('app.nested.deep.value'));
        $parsed->unset('app.nested.deep.value');
        $this->assertFalse($parsed->has('app.nested.deep.value'));
        $this->assertTrue($parsed->has('app.nested.deep')); // 父级仍应存在
        $this->assertTrue($parsed->has('app.nested'));       // 更高层父级也应存在

        // 测试删除不存在的键不应报错
        $parsed->unset('non.existent.key');
        $this->assertFalse($parsed->has('non.existent.key'));
    }

    public function testMergeMethod()
    {
        $parsed = new Parsed([
            'app' => [
                'name' => 'OriginalApp',
                'version' => '1.0.0'
            ],
            'database' => [
                'host' => 'original-host',
                'port' => 3306
            ]
        ]);

        $newData = [
            'app' => [
                'version' => '2.0.0',  // 应该覆盖原值
                'author' => 'TestAuthor'  // 应该新增
            ],
            'cache' => [  // 应该新增整个部分
                'driver' => 'redis',
                'host' => 'cache-host'
            ]
        ];

        $result = $parsed->merge($newData);

        // 验证返回值是自身实例（链式调用）
        $this->assertSame($parsed, $result);

        // 验证合并结果
        $this->assertEquals('OriginalApp', $parsed->get('app.name')); // 保持原值
        $this->assertEquals('2.0.0', $parsed->get('app.version'));   // 被新值覆盖
        $this->assertEquals('TestAuthor', $parsed->get('app.author')); // 新增值
        $this->assertEquals('original-host', $parsed->get('database.host')); // 保持原值
        $this->assertEquals('redis', $parsed->get('cache.driver'));   // 新增部分
        $this->assertEquals('cache-host', $parsed->get('cache.host')); // 新增部分
    }

    public function testGetWithDefaultValues()
    {
        $parsed = new Parsed([
            'app' => [
                'name' => 'TestApp'
            ]
        ]);

        // 测试存在的键
        $this->assertEquals('TestApp', $parsed->get('app.name'));
        
        // 测试不存在的键，有默认值
        $this->assertEquals('default_value', $parsed->get('app.description', 'default_value'));
        
        // 测试不存在的键，无默认值
        $this->assertNull($parsed->get('app.description'));
        
        // 测试深层嵌套不存在的键
        $this->assertEquals('fallback', $parsed->get('app.settings.theme.color', 'fallback'));
    }

    public function testParseMethodWithDifferentFileTypes()
    {
        // 测试JSON
        $jsonContent = ['format' => 'json', 'test' => true];
        $jsonFile = tempnam(sys_get_temp_dir(), 'test_json') . '.json';
        file_put_contents($jsonFile, json_encode($jsonContent));
        
        $parsedJson = $this->parser->parse($jsonFile);
        $this->assertEquals(true, $parsedJson->get(pathinfo($jsonFile, PATHINFO_FILENAME) . '.test'));
        
        unlink($jsonFile);

        // 测试YAML
        $yamlContent = "format: yaml\ntest: true";
        $yamlFile = tempnam(sys_get_temp_dir(), 'test_yaml') . '.yml';
        file_put_contents($yamlFile, $yamlContent);
        
        $parsedYaml = $this->parser->parse($yamlFile);
        $this->assertEquals('yaml', $parsedYaml->get(pathinfo($yamlFile, PATHINFO_FILENAME) . '.format'));
        
        unlink($yamlFile);
    }

    public function testConstructorWithEmptyVars()
    {
        $parser = new FileParser();
        $this->assertInstanceOf(Parsed::class, $parser->var);
        $this->assertInstanceOf(Parsed::class, $parser->parsed);
        
        // 验证空变量不会导致错误
        $emptyParser = new FileParser([]);
        $this->assertInstanceOf(Parsed::class, $emptyParser->var);
    }

    public function testParsedGetWithNumericKeys()
    {
        $parsed = new Parsed([
            'array' => [
                0 => 'first',
                1 => 'second',
                'nested' => [
                    0 => 'nested_first',
                    'key' => 'value'
                ]
            ]
        ]);

        // 测试数字键的访问
        $this->assertEquals('first', $parsed->get('array.0'));
        $this->assertEquals('second', $parsed->get('array.1'));
        $this->assertEquals('nested_first', $parsed->get('array.nested.0'));
        $this->assertEquals('value', $parsed->get('array.nested.key'));
    }

    public function testParsedGetWithSpecialCharactersInKeys()
    {
        $parsed = new Parsed([
            'special_keys' => 'value1',  // 键名不包含点号，可以正常访问
            'key-with-dash' => 'value2',
            'key_with_underscore' => 'value3',
            'normal' => [
                'sub_key' => 'value4'  // 子键也不包含点号
            ]
        ]);

        // 测试特殊字符键的访问
        $this->assertEquals('value1', $parsed->get('special_keys'));
        $this->assertEquals('value2', $parsed->get('key-with-dash'));
        $this->assertEquals('value3', $parsed->get('key_with_underscore'));
        $this->assertEquals('value4', $parsed->get('normal.sub_key'));
        
        // 演示包含点号的键如何访问
        $parsedWithDot = new Parsed([
            'key.with.dots' => 'dot_value'
        ]);
        $this->assertEquals('dot_value', $parsedWithDot['key.with.dots']);
        $this->assertEquals('dot_value', $parsedWithDot->get('key.with.dots')); // 实际上 get 方法也能访问包含点号的键
        
        // 验证嵌套访问（单独测试）
        $parsedNested = new Parsed([
            'key' => [
                'with' => [
                    'dots' => 'nested_value'
                ]
            ]
        ]);
        
        $this->assertEquals('nested_value', $parsedNested->get('key.with.dots')); // 嵌套访问
        
        // 验证点号键（单独测试）
        $parsedDirect = new Parsed([
            'key.with.dots' => 'direct_value'
        ]);
        
        $this->assertEquals('direct_value', $parsedDirect->get('key.with.dots')); // 直接键访问
    }

    public function testParsedSetWithExistingNonArrayValue()
    {
        $parsed = new Parsed([
            'simple' => 'string_value',
            'numeric' => 123,
            'boolean' => true
        ]);

        // 当键已存在但不是数组时，set 方法应该将其转换为数组
        $parsed->set('simple.nested', 'nested_value');
        
        $this->assertTrue($parsed->has('simple.nested'));
        $this->assertEquals('nested_value', $parsed->get('simple.nested'));
        
        // 原始值应该作为索引 0 存储
        $simpleVal = $parsed->get('simple');
        $this->assertIsArray($simpleVal);
        $this->assertEquals('string_value', $simpleVal[0]);
        $this->assertEquals('nested_value', $simpleVal['nested']);
    }

    public function testParsedMergeWithMixedDataTypes()
    {
        $parsed = new Parsed([
            'string_val' => 'original',
            'array_val' => ['item1', 'item2'],
            'nested' => [
                'keep' => 'this',
                'replace' => 'original'
            ]
        ]);

        $newData = [
            'string_val' => 'replaced',  // 应该替换
            'array_val' => ['item3', 'item4'],  // 应该合并
            'nested' => [
                'replace' => 'replaced',  // 应该替换
                'new' => 'added'  // 应该新增
            ],
            'new_section' => [
                'key' => 'value'
            ]
        ];

        $parsed->merge($newData);

        // 验证简单值被替换
        $this->assertEquals('replaced', $parsed->get('string_val'));
        
        // 验证数组被合并（不是替换）
        $this->assertEquals(['item1', 'item2', 'item3', 'item4'], $parsed->get('array_val'));
        
        // 验证嵌套数组的合并
        $this->assertEquals('this', $parsed->get('nested.keep'));  // 保持
        $this->assertEquals('replaced', $parsed->get('nested.replace'));  // 替换
        $this->assertEquals('added', $parsed->get('nested.new'));  // 新增
        
        // 验证新部分被添加
        $this->assertEquals('value', $parsed->get('new_section.key'));
    }

    public function testParsedHasWithDeeplyNestedStructure()
    {
        $parsed = new Parsed([
            'level1' => [
                'level2' => [
                    'level3' => [
                        'level4' => [
                            'deep_value' => 'found'
                        ]
                    ]
                ]
            ]
        ]);

        // 测试深度嵌套的键存在性
        $this->assertTrue($parsed->has('level1'));
        $this->assertTrue($parsed->has('level1.level2'));
        $this->assertTrue($parsed->has('level1.level2.level3'));
        $this->assertTrue($parsed->has('level1.level2.level3.level4'));
        $this->assertTrue($parsed->has('level1.level2.level3.level4.deep_value'));
        
        // 测试不存在的深度嵌套键
        $this->assertFalse($parsed->has('level1.level2.level3.level4.nonexistent'));
        $this->assertFalse($parsed->has('level1.level2.level3.level4.level5.deep_value'));
        $this->assertFalse($parsed->has('nonexistent.level2.level3.level4.deep_value'));
    }

    public function testParsedUnsetEdgeCases()
    {
        $parsed = new Parsed([
            'simple' => 'value',
            'nested' => [
                'deep' => [
                    'remove_me' => 'gone',
                    'keep_me' => 'stay'
                ],
                'another' => 'value'
            ],
            'array' => [
                'index0',
                'index1',
                'assoc_key' => 'assoc_value'
            ]
        ]);

        // 测试删除存在的键
        $this->assertTrue($parsed->has('simple'));
        $parsed->unset('simple');
        $this->assertFalse($parsed->has('simple'));

        // 测试删除深度嵌套键
        $this->assertTrue($parsed->has('nested.deep.remove_me'));
        $parsed->unset('nested.deep.remove_me');
        $this->assertFalse($parsed->has('nested.deep.remove_me'));
        $this->assertTrue($parsed->has('nested.deep.keep_me')); // 兄弟节点应保留
        $this->assertTrue($parsed->has('nested.another'));    // 父节点的兄弟应保留

        // 测试删除不存在的键不应该报错
        $this->assertFalse($parsed->has('nonexistent'));
        $parsed->unset('nonexistent');
        $this->assertFalse($parsed->has('nonexistent'));

        // 测试删除深度不存在的键
        $this->assertFalse($parsed->has('nested.nonexistent.deep'));
        $parsed->unset('nested.nonexistent.deep');
        $this->assertFalse($parsed->has('nested.nonexistent.deep'));
    }

    public function testParsedArrayAccessInterfaceMethods()
    {
        $parsed = new Parsed(['test' => 'value']);

        // 测试 offsetExists
        $this->assertTrue($parsed->offsetExists('test'));
        $this->assertFalse($parsed->offsetExists('nonexistent'));

        // 测试 offsetGet
        $this->assertEquals('value', $parsed->offsetGet('test'));
        $this->assertNull($parsed->offsetGet('nonexistent'));

        // 测试 offsetSet
        $parsed->offsetSet('new_key', 'new_value');
        $this->assertEquals('new_value', $parsed->offsetGet('new_key'));
        $this->assertEquals('new_value', $parsed->get('new_key'));

        // 测试 offsetUnset
        $this->assertTrue($parsed->offsetExists('new_key'));
        $parsed->offsetUnset('new_key');
        $this->assertFalse($parsed->offsetExists('new_key'));
    }

    public function testParsedWithEmptyArraysAndNullValues()
    {
        $parsed = new Parsed([
            'empty_array' => [],
            'null_value' => null,
            'zero_value' => 0,
            'false_value' => false,
            'empty_string' => '',
            'nested' => [
                'empty_array' => [],
                'null_value' => null
            ]
        ]);

        // 测试各种“假值”的处理
        $this->assertEquals([], $parsed->get('empty_array'));
        $this->assertNull($parsed->get('null_value'));
        $this->assertSame(0, $parsed->get('zero_value'));
        $this->assertFalse($parsed->get('false_value'));
        $this->assertSame('', $parsed->get('empty_string'));
        
        // 验证这些值确实存在
        $this->assertTrue($parsed->has('empty_array'));
        $this->assertTrue($parsed->has('null_value'));
        $this->assertTrue($parsed->has('zero_value'));
        $this->assertTrue($parsed->has('false_value'));
        $this->assertTrue($parsed->has('empty_string'));
        
        // 测试嵌套的空值
        $this->assertTrue($parsed->has('nested.empty_array'));
        $this->assertTrue($parsed->has('nested.null_value'));
        $this->assertEquals([], $parsed->get('nested.empty_array'));
        $this->assertNull($parsed->get('nested.null_value'));
    }
}
