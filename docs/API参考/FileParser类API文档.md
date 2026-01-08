# FileParser 类 API 文档

`FileParser` 是 FastD\Config 库的主要配置文件解析器类，负责加载、解析和处理不同格式的配置文件。

## 类定义

```php
class FileParser
{
    public Parsed $var;
    public Parsed $parsed;
    
    public function __construct(array|string $vars = [], public Parsed $parsed = new Parsed());
    public function load(string $file): array;
    public function parse(string $file): Parsed;
    protected function replace(mixed $data): mixed;
}
```

## 属性

### $var

- **类型**: `Parsed`
- **说明**: 存储变量配置的 Parsed 对象，用于变量替换功能

### $parsed

- **类型**: `Parsed`
- **说明**: 存储解析后配置数据的 Parsed 对象

## 构造函数

### `__construct(array|string $vars = [], public Parsed $parsed = new Parsed())`

初始化 FileParser 实例。

#### 参数

| 参数名 | 类型 | 默认值 | 说明 |
|--------|------|--------|------|
| $vars | array\|string | `[]` | 变量配置，可以是数组或配置文件路径 |
| $parsed | Parsed | `new Parsed()` | 用于存储解析后配置的 Parsed 对象 |

#### 说明

- 如果 `$vars` 是字符串，则将其视为配置文件路径并加载
- 如果 `$vars` 是数组，则直接使用该数组作为变量配置
- 初始化 `$var` 属性为包含变量数据的 `Parsed` 对象

#### 示例

```php
use FastD\Config\FileParser;

// 使用数组作为变量配置
$parser1 = new FileParser([
    'database_host' => 'localhost',
    'database_port' => 3306
]);

// 使用配置文件作为变量配置
$parser2 = new FileParser('variables.yml');

// 自定义 Parsed 对象
$customParsed = new Parsed(['existing' => 'config']);
$parser3 = new FileParser([], $customParsed);
```

## 公共方法

### `load(string $file): array`

加载并解析配置文件，返回解析后的数组数据。

#### 参数

| 参数名 | 类型 | 说明 |
|--------|------|------|
| $file | string | 配置文件的路径 |

#### 返回值

- **类型**: `array`
- **说明**: 解析后的配置数据数组

#### 异常

- `Exception`: 当文件格式不受支持时抛出

#### 支持的文件格式

| 扩展名 | 解析方法 | 说明 |
|--------|----------|------|
| `.json` | `json_decode()` | JSON 格式配置文件 |
| `.yml` | `Yaml::parseFile()` | YAML 格式配置文件 |
| `.ini` | `parse_ini_file()` | INI 格式配置文件 |
| `.php` | `include` | PHP 格式配置文件（返回数组） |

#### 示例

```php
use FastD\Config\FileParser;

$parser = new FileParser();

// 加载 JSON 配置文件
$jsonConfig = $parser->load('config.json');

// 加载 YAML 配置文件
$yamlConfig = $parser->load('config.yml');

// 加载 INI 配置文件
$iniConfig = $parser->load('config.ini');

// 加载 PHP 配置文件
$phpConfig = $parser->load('config.php');
```

### `parse(string $file): Parsed`

加载、解析配置文件并执行变量替换，返回 Parsed 对象。

#### 参数

| 参数名 | 类型 | 说明 |
|--------|------|------|
| $file | string | 配置文件的路径 |

#### 返回值

- **类型**: `Parsed`
- **说明**: 包含解析后配置数据的 Parsed 对象

#### 处理流程

1. 使用 `load()` 方法加载配置文件
2. 对加载的数据执行变量替换
3. 将解析后的配置合并到 `$parsed` 对象中
4. 返回 `$parsed` 对象

#### 示例

```php
use FastD\Config\FileParser;

$parser = new FileParser([
    'database_host' => 'localhost',
    'database_port' => 3306
]);

// 解析配置文件并应用变量替换
$config = $parser->parse('app.yml');

// 访问解析后的配置
echo $config->get('database.host'); // localhost
echo $config->get('database.port'); // 3306
```

## 受保护方法

### `replace(mixed $data): mixed`

递归处理数据中的变量替换。

#### 参数

| 参数名 | 类型 | 说明 |
|--------|------|------|
| $data | mixed | 需要处理的数据 |

#### 返回值

- **类型**: `mixed`
- **说明**: 处理后的数据，变量已被替换

#### 处理逻辑

- **数组类型**: 递归处理每个元素
- **字符串类型**: 执行变量替换（匹配 `%variable_name%` 格式）
- **其他类型**: 直接返回

#### 变量替换规则

- 使用正则表达式 `/([a-zA-Z0-9._]+)%/` 匹配变量
- 在 `$var` 对象中查找对应的变量值
- 如果变量存在则替换，否则保持原值
- 支持在字符串中嵌入多个变量

#### 示例

```php
use FastD\Config\FileParser;

$parser = new FileParser([
    'name' => 'John',
    'age' => 25
]);

// 字符串变量替换
$result = $parser->replace('%name% is %age% years old');
// 结果: 'John is 25 years old'

// 数组变量替换
$data = [
    'greeting' => 'Hello, %name%!',
    'info' => [
        'age' => '%age%',
        'status' => 'Active'
    ]
];
$result = $parser->replace($data);
// 结果: [
//     'greeting' => 'Hello, John!',
//     'info' => [
//         'age' => 25,
//         'status' => 'Active'
//     ]
// ]
```

## 使用示例

### 基础使用

```php
use FastD\Config\FileParser;

// 创建解析器实例
$parser = new FileParser();

// 加载配置文件
$config = $parser->parse('config.json');

// 访问配置值
echo $config->get('database.host');
```

### 变量替换使用

```php
use FastD\Config\FileParser;

// 带变量配置的解析器
$parser = new FileParser([
    'database_host' => 'localhost',
    'database_port' => 3306,
    'app_name' => 'MyApp'
]);

// 解析包含变量的配置文件
$config = $parser->parse('app.yml');

// 配置文件中的 %database_host% 会被替换为 localhost
echo $config->get('database.host'); // localhost
```

### 多文件配置合并

```php
use FastD\Config\FileParser;

$parser = new FileParser();

// 解析多个配置文件，配置会被合并
$databaseConfig = $parser->parse('database.yml');
$cacheConfig = $parser->parse('cache.yml');
$appConfig = $parser->parse('app.yml');

// 所有配置都存储在同一个 Parsed 对象中
echo $databaseConfig->get('database.host'); // 可以访问数据库配置
echo $cacheConfig->get('cache.driver');     // 可以访问缓存配置
```

## 注意事项

1. **文件格式支持**: 确保配置文件扩展名正确，以便系统能识别正确的解析方法
2. **变量命名**: 变量名只能包含字母、数字、点号和下划线
3. **错误处理**: `load()` 方法在遇到不支持的文件格式时会抛出异常
4. **性能考虑**: 对于大型配置文件，变量替换可能会消耗一定时间
5. **安全性**: 避免在配置文件中硬编码敏感信息，应通过变量配置传入